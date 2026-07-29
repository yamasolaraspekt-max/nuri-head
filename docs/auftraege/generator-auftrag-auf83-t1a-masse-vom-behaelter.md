# AUF-83-T1a — Die Insel nimmt ihre Maße vom Behälter

*Planner, 29.07.2026, 08:25 CEST. Erster Schritt von AUF-83, von Yama am 29.07. um 08:20 freigegeben:
„jetzt sieht es besser aus und ich gebe das jetzt frei bitte diese aufgabe vorziehen und ich möchte
das sehen auf den dashboard bzw bildschirm".*

**Grundlage:** `docs/planner/entwurf-studio-in-ticket-shell-2026-07-29.html` ·
`docs/planner/t1-entscheidungsgrundlage-ticket-shell-2026-07-29.md`

```yaml
auftrag:
  id: AUF-83-T1a
  status: aktiv
  spur: A
  heimat: ticket
  ziel: >
    Breite und Hoehe der Hausplaner-Insel kommen aus ihrem Behaelter statt aus Fensterkonstanten.
    Danach ist sie einbettbar (T1b) und overlay-faehig (T5), ohne dass eine weitere Zeile
    Layout-Rechnung entsteht.
  nicht_ziel: >
    KEIN Blade-Umbau, KEIN @extends, keine Aenderung an Routen, Rechten oder Auth — das ist T1b.
    KEINE neue Optik: bei unveraenderten Panelbreiten muss der Bildschirm pixelgleich bleiben.
    KEINE Umstellung von Inline-Stilen — AUF-38 Scheibe 7 bleibt gesperrt und wird nicht angefasst.

scope:
  population_command: >
    grep -n 'innerWidth' resources/planner/hausplaner/app/HausplanerApp.tsx &&
    grep -rn '100vh' resources/views/admin/hausplaner/
  population_at_writing: >
    Drei Rechnungen: HausplanerApp.tsx:369 (Breite), studio.blade.php:24 und objekt.blade.php:27
    (Hoehe). Dazu EINE geerbte Zusage in Z1442. Messung des Planners, KEINE Bedingung.
  pfade:
    - resources/planner/hausplaner/app/HausplanerApp.tsx
    - resources/views/admin/hausplaner/studio.blade.php
    - resources/views/admin/hausplaner/objekt.blade.php
    - resources/planner/hausplaner/hausplaner.css
    - resources/planner/hausplaner/__tests__/breiten.test.ts
  ausschluesse:
    - stelle: "die 78 offenen Inline-Stellen in HausplanerApp.tsx"
      grund: >
        Das ist AUF-38 Scheibe 7 und ausdruecklich gesperrt. Zwei Posten in derselben Datei sind
        die Kollision, die §13 verhindert. Wer hier eine Inline-Stelle umstellt, baut Scheibe 7
        nebenbei — verboten.
      entschieden_von: planner

kriterien:
  - id: K-01
    aussage: "Die Planbreite folgt dem Behaelter, nicht dem Fenster."
    typ: behavioural
    kritikalitaet: P1
    pruefung:
      typ: visuell
      schritte: >
        Studio bei 1440 px oeffnen. Die Insel in einen Behaelter mit 900 px Breite setzen
        (Testseite oder DevTools). Der Plan muss die 900 px ausfuellen, nicht 1440 minus 488.
      erwartet: "gemessene Planbreite == Behaelterbreite minus tatsaechlicher Panelbreiten"
    beleg: zwei getBoundingClientRect-Ausgaben, Behaelter und Plan
    ausgefuehrt_von: evaluator
    begruendung: >
      `HausplanerApp.tsx:369` rechnet heute `innerWidth - 220 - 268`. Solange das steht, weiss der
      Plan nichts von seinem Behaelter — und kein Panel kann als Overlay laufen, weil sein
      Zuklappen die Rechnung nicht erreicht.

  - id: K-02
    aussage: "Die Inselhoehe folgt dem Behaelter, nicht dem Fensterausschnitt."
    typ: absence
    kritikalitaet: P1
    pruefung:
      befehl: "grep -rn '100vh' resources/views/admin/hausplaner/ resources/planner/hausplaner/"
      erwartet: "kein Treffer mehr fuer #hausplaner-root"
    beleg: grepausgabe
    partner: >
      presence-Partner nach R2: derselbe Befehl ohne Pfadfilter muss Treffer liefern (es gibt
      `100vh` anderswo im Projekt) — sonst prueft der Befehl nichts.
    begruendung: >
      `min-height: calc(100vh - 46px)` mit der Hoehe der EIGENEN Blade-Leiste. In der Ticket-Shell
      sitzt `@yield('content')` in `.main-content-scroll` — `100vh` erzeugt dort einen ZWEITEN
      Bildlauf und schiebt den Zeichenbereich unter die Falz.

  - id: K-03
    aussage: "Kein zweiter Bildlauf, wenn die Insel in einem Scroll-Container sitzt."
    typ: behavioural
    kritikalitaet: P1
    pruefung:
      typ: visuell
      schritte: >
        Testseite: `#hausplaner-root` in einen Behaelter mit `overflow:auto` und fester Hoehe
        setzen, darueber eine 52-px-Kopfzeile.
      erwartet: >
        `document.scrollingElement.scrollHeight == clientHeight` — kein Seiten-Bildlauf.
        Der Plan endet sichtbar innerhalb des Behaelters.
    beleg: scrollHeight/clientHeight vorher und nachher
    ausgefuehrt_von: evaluator

  - id: K-04
    aussage: "Bei unveraenderten Panelbreiten ist der Bildschirm pixelgleich."
    typ: behavioural
    kritikalitaet: P1
    pruefung:
      typ: visuell
      schritte: "headful, 1440 / 1024 / 375 px, Studio im Expertenmodus, ganzseitig"
      erwartet: "sha256 der Bildschirmfotos identisch zu vorher"
    beleg: sha256-Paare je Viewport
    ausgefuehrt_von: evaluator
    begruendung: >
      Dieser Auftrag aendert das VERFAHREN, nicht das Bild. Sieht es anders aus, ist etwas
      anderes passiert als beauftragt — dann ist die Abweichung der Befund, nicht der Haken.

  - id: K-05
    aussage: "Die geerbte Zusage ist mitgezogen und prueft die Wirkung."
    typ: coverage
    kritikalitaet: P1
    pruefung:
      befehl: "grep -n '220\\|268\\|innerWidth' resources/planner/hausplaner/__tests__/breiten.test.ts"
      erwartet: >
        Die Zusage haelt nicht mehr die FORMEL fest (`innerWidth - 220 - 268`), sondern die
        WIRKUNG: der Plan fuellt den verbleibenden Raum seines Behaelters.
    beleg: testausgabe + Wortlaut der geaenderten Zusage
    begruendung: >
      `HausplanerApp.tsx:1442` traegt die Formel als Zusage. Wer sie nicht mitzieht, bekommt ein
      rotes Gate ohne Fehler — der sechste Beleg desselben Bautyps in diesem Projekt.
    such_auflage: >
      Gesucht wird ueber die EIGENSCHAFTSNAMEN (`width`, `gridTemplateColumns`, `minHeight`),
      NICHT ueber die Woerter `style` oder `inline`. Auflage aus Scheibe 5.

  - id: K-06
    aussage: "Keine Inline-Stelle aus Scheibe 7 ist mitgewandert."
    typ: absence
    kritikalitaet: P1
    pruefung:
      befehl: "node scripts/statische-inline-stile.mjs resources/planner/hausplaner/app/HausplanerApp.tsx"
      erwartet: "die Zahl der offenen Stellen ist unveraendert (heute 78)"
    beleg: rohausgabe vorher/nachher
    begruendung: >
      Die Sperre auf Scheibe 7 ist mit einem gemessenen Grund gesetzt. Dieser Auftrag darf sie
      nicht durch die Hintertuer aufweichen.

  - id: K-07
    aussage: "Gates ohne Regression."
    typ: presence
    kritikalitaet: P1
    pruefung:
      befehl: "npm run tsc:hausplaner && npm run schema:hausplaner:check && npm run test:hausplaner && npm run test:hausplaner:dom && npm run build:hausplaner && php artisan test"
      erwartet: "0/0/0/0/0, Insel-Testzahl nicht gefallen, PHP 789"
    beleg: testzaehler vorher/nachher

selbstnachweis:
  quittung_zuerst: "Readiness-Quittung nach §2, bevor eine Zeile entsteht."
  gegenprobe: >
    Die alte Rechnung wieder einsetzen ⇒ K-01 und K-05 muessen rot werden. Faellt nur eine,
    prueft die andere die Gestalt statt der Wirkung.
  rueckweg: >
    Revert ueber vier Dateien. Kein Datenpfad, kein Schema, keine Migration, keine Route.
    Zurueckdrehen einer Probe NIE mit `git checkout` — Kopie beiseite und `cp` zurueck,
    mit `diff -q` als Beleg (Auflage aus AUF-38-MW-N2).
```

---

## Warum dieser Schritt zuerst kommt

Yamas Auftrag hieß *„räume auf, der Zeichenbereich soll mehr Platz bekommen"*. **Aufräumen allein
gibt keinen Platz.** Die Breite des Plans kommt heute nicht aus dem Layout, sondern aus einer
Subtraktion mit zwei fest verdrahteten Zahlen. Kopfleisten zusammenzulegen ändert daran nichts.

**Und derselbe Fehler steckt in der Höhe.** Beide Rechnungen messen gegen das Fenster statt gegen
den Behälter — deshalb ist die Insel heute weder in die Ticket-Shell einbettbar (T1b) noch
overlay-fähig (T5). **Es sind nicht drei Aufgaben, sondern dreimal dieselbe.**

**Dieser Auftrag ist zugleich der erste Zerlegungsschritt von AUF-48.** Wenn die Maße an *einer*
Stelle liegen statt an dreien, ist die spätere Zerlegung von `HausplanerApp.tsx` Vorarbeit statt
Nacharbeit. Das war die Auflage, mit der ich die Frage „AUF-48 vorziehen?" aufgelöst habe.

## Die Auflage, die den Umfang begrenzt

`HausplanerApp.tsx` trägt **78 offene Inline-Stellen** (AUF-38 Scheibe 7) und wird von **AUF-48**
beansprucht. **Dieser Auftrag fasst genau die Layout-Rechnung an — nichts sonst in dieser Datei.**

Wenn beim Bauen auffällt, dass eine Inline-Stelle im Weg steht: **melden, nicht mitnehmen.** Ein
Auftrag, der nebenbei eine gesperrte Scheibe baut, macht beide unprüfbar.

## Reihenfolge

1. **Dieses Blatt (T1a)** — Maße vom Behälter. Danach ist der Weg frei.
2. **T1b** — die Blades erben von `admin.layouts.app`; die Ticket-Navigation erscheint.
3. **T2** — die zweite und dritte Navigation fallen (Blade-Leiste, `hp-navi-*`, Marke, Doppelung).
4. **T3** — Kopfleiste und Arbeitszeile; die 13-teilige Geschosszeile fällt weg.
5. **T5** — Eigenschaften-Panel klappbar, Escape-Stapel, Zustand je Arbeitsbereich.
