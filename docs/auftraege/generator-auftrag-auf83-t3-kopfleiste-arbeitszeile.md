# AUF-83-T3 — Eine Kopfleiste, eine Arbeitszeile, und die Geschosszeile fällt

*Planner, 29.07.2026, 10:05 CEST. Vierter Schritt von AUF-83. Grundlage:
`docs/planner/entwurf-studio-in-ticket-shell-2026-07-29.html` (Abschnitte 03 und 04),
von Yama am 29.07. um 08:20 freigegeben.*

> **GESPERRT, bis `AUF-83-T2` gebaut ist.** Grund: T3 baut die Leiste, in die T2 die Inhalte
> freiräumt. Beide gleichzeitig hieße, an derselben Datei zwei Posten zu führen — §13.

```yaml
auftrag:
  id: AUF-83-T3
  status: gesperrt
  sperrgrund: "wartet auf den BAU von AUF-83-T2"
  spur: B
  heimat: ticket
  ziel: >
    Der Planer traegt EINE Kopfleiste (Projekt, Geschoss, Modus, Speichern) und EINE Arbeitszeile
    (Arbeitsbereiche, 2D/Split/3D, Werkzeuge, Suche). Die 13-teilige Geschosszeile im Zeichenbereich
    entfaellt; Anlegen, Duplizieren und Loeschen wandern in das Menue des Geschoss-Waehlers.
  nicht_ziel: >
    KEINE Aenderung an der Ticket-Shell. KEIN neues Bedienmuster (Kontextmenue, Doppelklick) —
    gemessen 0 Fundstellen in der Insel, das ist ein eigener Auftrag.
    KEINE Umstellung von Inline-Stilen in HausplanerApp.tsx — Scheibe 7 bleibt gesperrt.
    ANGEFASST WIRD DORT AUSSCHLIESSLICH DIE GESCHOSSZEILE.

scope:
  population_command: >
    sed -n '1195,1240p' resources/planner/hausplaner/app/HausplanerApp.tsx &&
    node scripts/statische-inline-stile.mjs resources/planner/hausplaner/app/HausplanerApp.tsx
  population_at_writing: >
    Die Geschosszeile traegt 13 Bedienelemente in vier voneinander unabhaengigen Aufgaben
    (Layout-Inventur 25.07., Befund B1): Rueckgaengig/Wiederholen · Geschoss-Navigation (111 px) ·
    ein Textfeld mit DEMSELBEN Wert wie der Waehler daneben · Anlegen/Duplizieren/Loeschen ·
    2D/Split/3D · Speichern. Scheibe 7 steht bei 78 offenen Inline-Stellen — diese Zahl muss
    unveraendert bleiben. Messung des Planners, KEINE Bedingung.
  pfade:
    - resources/planner/hausplaner/app/HausplanerStudio.tsx
    - resources/planner/hausplaner/app/HausplanerApp.tsx        # NUR die Geschosszeile
    - resources/planner/hausplaner/app/dashboard/GeschossFlaeche.tsx
    - resources/planner/hausplaner/hausplaner.css
  ausschluesse:
    - stelle: "alles in HausplanerApp.tsx ausser der Geschosszeile"
      grund: >
        AUF-38 Scheibe 7 und AUF-48 beanspruchen dieselbe Datei. Faellt beim Bauen eine
        Inline-Stelle im Weg auf: melden, nicht mitnehmen.
      entschieden_von: planner
    - stelle: "Kontextmenues und Doppelklick"
      grund: >
        Gemessen 0 `onContextMenu` und 0 `onDoubleClick` in der ganzen Insel. Ein neues
        Bedienmuster ist kein Aufraeumen; eigener Auftrag, sonst wird aus *aufraeumen* ein Neubau.
      entschieden_von: planner

kriterien:
  - id: K-01
    aussage: "Die Geschosszeile im Zeichenbereich ist fort."
    typ: absence
    kritikalitaet: P1
    pruefung:
      typ: visuell
      schritte: "1440 px, Expertenmodus, Zeichenbereich"
      erwartet: >
        Ueber dem Plan steht keine eigene Zeile mit Geschoss-Bedienung mehr. Die 13 Elemente sind
        verteilt: Rueckgaengig/Wiederholen/Speichern in die Kopfleiste, 2D/Split/3D in die
        Arbeitszeile, Geschoss-Waehler in die Kopfleiste, Anlegen/Duplizieren/Loeschen in sein Menue.
    beleg: Bildschirmfoto vorher/nachher + Zaehlung der Bedienelemente
    ausgefuehrt_von: evaluator

  - id: K-02
    aussage: "Das Textfeld mit dem doppelten Geschossnamen ist ersatzlos fort."
    typ: absence
    kritikalitaet: P1
    pruefung:
      typ: visuell
      erwartet: >
        Der Geschossname erscheint EINMAL. Heute steht er im Waehler UND direkt daneben in einem
        Eingabefeld — derselbe Wert, zweimal, nebeneinander.
    beleg: DOM-Auszug
    begruendung: >
      Das Umbenennen bleibt moeglich (im Menue des Waehlers). **Was faellt, ist die zweite Anzeige
      desselben Werts** — nicht die Faehigkeit.

  - id: K-03
    aussage: "Anlegen, Duplizieren und Loeschen liegen im Menue des Waehlers."
    typ: behavioural
    kritikalitaet: P1
    pruefung:
      typ: visuell
      schritte: "Geschoss-Waehler oeffnen"
      erwartet: >
        Ein Ort, an dem alles zum Geschoss steht: die Liste MIT Hoehenlage, darunter Anlegen,
        Darueber duplizieren, Loeschen. Alle drei Funktionen arbeiten wie vorher
        (`dupliziereGeschossJetzt` bleibt die Wahrheit, sie wird nur anders erreicht).
    beleg: Bildschirmfoto + eine ausgefuehrte Duplizierung
    ausgefuehrt_von: evaluator
    begruendung: >
      Yama: *„das feld etagen einfuegen nicht runter genommen.“* Im ersten Entwurf hatte ich nur
      den Waehler hochgezogen und die Bedienung daneben stehenlassen — die Zeile halb geraeumt und
      behauptet, sie falle weg.

  - id: K-04
    aussage: "Die Hoehenlage ist sichtbar."
    typ: presence
    kritikalitaet: P2
    pruefung:
      typ: visuell
      erwartet: "Am Waehler und in der Liste steht die Hoehenlage (`±0 mm`, `+2 750 mm`)."
    beleg: DOM-Auszug
    begruendung: >
      `geschossStapel.ts` fuehrt `elevation` und `hoehenLabel` bereits — **der Wert wird berechnet
      und nirgends gezeigt.** Zweiter Fall dieser Art in diesem Projekt (der erste: die Griffe in
      `auswahlDarstellung.ts`). Bestandscode-first: anzeigen, nicht bauen.

  - id: K-05
    aussage: "Die Arbeitszeile fuehrt die fuenf Arbeitsbereiche."
    typ: presence
    kritikalitaet: P1
    pruefung:
      typ: visuell
      erwartet: >
        Import & Nachzeichnen · Architektur · Bauphysik · Heizung · Elektro · PV — die Quelle ist
        `arbeitsbereiche.ts`, nicht eine neue Liste. **Import ist ausgegraut** (er besteht heute
        aus Namen, nicht aus Funktion) und sagt das auch.
    beleg: DOM-Auszug + Herkunft der Daten im Diff
    begruendung: >
      **Die Fachplaner SIND die Arbeitsbereiche.** Sie hier zu fuehren ersetzt den Baum aus T2 —
      eine Benennung statt zweier.

  - id: K-06
    aussage: "Die Befehlspalette ist sichtbar erreichbar."
    typ: presence
    kritikalitaet: P2
    pruefung:
      typ: visuell
      erwartet: "Ein Einstieg in der Arbeitszeile (`Suchen ⌘K`), der die vorhandene Palette oeffnet."
    beleg: Bildschirmfoto + der geoeffnete Dialog
    grenze: >
      **Nicht bauen — erreichbar machen.** `dashboard/palette.ts` speist aus der Registry,
      `tools/trefferSuche.ts` sucht. Eine zweite Aktivierungslogik waere ein Fehler.

  - id: K-07
    aussage: "Scheibe 7 ist unberuehrt."
    typ: absence
    kritikalitaet: P1
    pruefung:
      befehl: "node scripts/statische-inline-stile.mjs resources/planner/hausplaner/app/HausplanerApp.tsx"
      erwartet: >
        Die Zahl der offenen Stellen ist unveraendert — ODER die Abweichung ist Zeile fuer Zeile
        begruendet, weil die Geschosszeile selbst Inline-Stellen trug.
    beleg: rohausgabe vorher/nachher + Begruendung je Abweichung
    begruendung: >
      Hier ist eine Abweichung ERLAUBT, anders als in T1a — die Geschosszeile faellt ja weg und
      nimmt ihre Stellen mit. **Was nicht erlaubt ist: eine Stelle umzustellen, die bleibt.**

  - id: K-08
    aussage: "Der Zeichenbereich gewinnt messbar Hoehe."
    typ: coverage
    kritikalitaet: P1
    pruefung:
      typ: visuell
      schritte: "1440 px, getBoundingClientRect der Buehne vorher und nachher"
      erwartet: "waechst; um wie viel, wird gemessen und berichtet — kein Sollwert"
    beleg: zwei getBoundingClientRect-Ausgaben
    ausgefuehrt_von: evaluator

  - id: K-09
    aussage: "Geerbte Zusagen vollstaendig, nicht nach Muster."
    typ: coverage
    kritikalitaet: P1
    pruefung:
      befehl: "grep -rl 'HausplanerApp\\|HausplanerStudio\\|GeschossFlaeche' resources/planner/hausplaner/__tests__/"
      erwartet: "die LISTE steht in der Quittung, jede Datei angesehen"
    beleg: Dateiliste + je Datei ein Satz
    barriere: "R9-Barriere vom 29.07., 10:00. Bei HausplanerApp allein sind es 22 Dateien."

  - id: K-10
    aussage: "Gates ohne Regression."
    typ: presence
    kritikalitaet: P1
    pruefung:
      befehl: "npm run tsc:hausplaner && npm run schema:hausplaner:check && npm run test:hausplaner && npm run test:hausplaner:dom && npm run build:hausplaner"
      erwartet: "0/0/0/0/0"
    beleg: testzaehler vorher/nachher

selbstnachweis:
  quittung_zuerst: "Readiness-Quittung nach §2, mit der Dateiliste aus K-09."
  gegenprobe: >
    Die Geschosszeile wieder einsetzen ⇒ K-01 und K-02 muessen rot werden.
  sichtprobe: >
    REIN VISUELL. Drei Viewports. **Und der Waehler ist ein Menue** — ein Standardbild zeigt es
    nicht. Es muss geoeffnet erfasst werden, wie beim Toast in Scheibe 4 und beim Dialog in
    Scheibe 5.
```

---

## Warum das Geschoss in die Kopfleiste gehört

Die Layout-Inventur nennt es **„das Tor"**: ein angelegtes Geschoss entsperrt auf einen Schlag
**34 von 110 Werkzeugen**. Die folgenreichste Handlung der ganzen Oberfläche steckte in einem
**111-px-Dropdown zwischen „Rückgängig" und „Speichern"** — und daneben stand derselbe Name noch
einmal in einem Eingabefeld.

**Vier unabhängige Aufgaben in einer Zeile** (Verlauf · Geschoss · Ansicht · Speichern) sind kein
Layoutproblem, sondern ein Informationsproblem: eine Fläche mit vier Jobs sagt nicht mehr, was sie
ist. Deshalb wandern sie dorthin, wo sie hingehören — und nicht alle an denselben neuen Ort.

## Was danach noch offen bleibt

**T5** — das Eigenschaften-Panel klappbar, Escape-Stapel, Zustand je Arbeitsbereich. Die
Vorbedingung liegt seit T1a: der Beobachter hängt an den **einzelnen** Schienen, nicht nur an der
Reihe. Das war die Zugabe des Generators, die niemand beauftragt hatte.
