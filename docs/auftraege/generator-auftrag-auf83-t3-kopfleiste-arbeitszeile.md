# AUF-83-T3 — Eine Kopfleiste, eine Arbeitszeile, und die Geschosszeile fällt

*Planner, 29.07.2026, 10:05 CEST. Vierter Schritt von AUF-83. Grundlage:
`docs/planner/entwurf-studio-in-ticket-shell-2026-07-29.html` (Abschnitte 03 und 04),
von Yama am 29.07. um 08:20 freigegeben.*

> **GESPERRT, bis `AUF-83-T2` gebaut ist.** Grund: T3 baut die Leiste, in die T2 die Inhalte
> freiräumt. Beide gleichzeitig hieße, an derselben Datei zwei Posten zu führen — §13.

```yaml
auftrag:
  id: AUF-83-T3
  status: aktiv            # entsperrt: T2 ist gebaut (45656ac1 / 86059540)
  spur: A                  # 21:40 KORRIGIERT, war B — der Evaluator hat es belegt, nicht behauptet
  nachtrag: "29.07. 21:40 — Grundgesamtheit korrigiert · Vorher-Wert-Pflicht · Spur A"
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
    KORRIGIERT 29.07., 21:40 — meine Zahl war ueberholt, und ich hatte sie nicht nachgemessen.
    ICH SCHRIEB: 13 Bedienelemente in vier Aufgaben (Layout-Inventur vom 25.07., Befund B1).
    GEMESSEN (Generator, 21:31, unmittelbar vor dem Schreiben nach R14):
      Zeile Z1183-1251 traegt 1 Knopf (Speichern) + <GeschossFlaeche> · 0 <select> · 0 <input>
    AUF-43 hat seither zwei P1-Kriterien dieses Blattes bereits erfuellt.
    DER ABRISS DER GESCHOSSZEILE HAT STATTGEFUNDEN — nur nicht durch diesen Auftrag.
    Scheibe 7: 138 gesamt / 78 statisch / 78 offen, unveraendert zu halten.
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
    status: BEREITS ERFUELLT DURCH AUF-43      # bestaetigt vom Planner, 21:40
    nachweis: "GeschossFlaeche.tsx:138 — das Eingabefeld sitzt im Menue des Waehlers, nicht in der Zeile"
    hinweis: >
      NICHT als eigene Leistung berichten. Wer das nicht weiss, liest den Bau als Erfolg an einer
      Stelle, an der nichts geschehen ist — und die naechste Inventur schreibt die Zahl aus dem
      Blatt fort statt der gemessenen.
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
    status: BEREITS ERFUELLT DURCH AUF-43      # bestaetigt vom Planner, 21:40
    nachweis: "GeschossFlaeche.tsx Z157/158/163 — alle drei sind Knoepfe im Menue"
    hinweis: "NICHT als eigene Leistung berichten. Siehe K-02."
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
    vorher_wert_pflicht: >
      DER GENERATOR HAELT DEN VORHER-WERT FEST, BEVOR ER BAUT — eine Zeile in der Quittung:
      getBoundingClientRect der LEINWAND (nicht der Wurzel), 1440 px, Expertenmodus.
      OHNE DIESE ZEILE IST DAS KRITERIUM NICHT ABNEHMBAR und der Bau beginnt nicht.
    pruefung:
      typ: visuell
      schritte: "1440 px, getBoundingClientRect der LEINWAND, gegen den Wert aus der Quittung"
      erwartet: "waechst; um wie viel, wird gemessen und berichtet — kein Sollwert"
    beleg: der Vorher-Wert aus der Quittung + die Nachher-Messung
    ausgefuehrt_von: evaluator
    barriere: >
      R9-BARRIERE, 29.07. 21:40 — ZWEITE WIEDERHOLUNG DERSELBEN KLASSE. T1a/K-07 und T2/K-06 sind
      beide unmessbar geworden, weil ihr Vorher-Wert nirgends stand und der Baum weiterlief.
      VORSCHLAG DES EVALUATORS, unveraendert uebernommen: "Ein Kriterium, das einen Vorher-Wert
      braucht, muss ihn im Auftrag festhalten lassen — vom Generator vor dem Bau, in einer Zeile.
      Wer ihn der Abnahme ueberlaesst, verliert ihn in dem Moment, in dem der Commit landet."
      GILT AB SOFORT FUER JEDES KRITERIUM MIT VORHER-BEZUG, in jedem Auftrag.

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


---

## Drei Entscheidungen vom 29.07., 21:40 — alle drei kommen von euch, nicht von mir

**1. Die Grundgesamtheit war überholt, und ich hatte sie nicht nachgemessen.** Meine 13
Bedienelemente stammten aus der Layout-Inventur vom 25.07. **AUF-43 hat zwei P1-Kriterien dieses
Blattes seither bereits erfüllt** — das Textfeld sitzt im Menü, Anlegen/Duplizieren/Löschen sind
Knöpfe dort. **Bestätigt.** Sie werden als *bereits erfüllt durch AUF-43* geführt, **nicht als
eigene Leistung** — genau aus dem Grund, den der Generator nennt: sonst liest jemand den Bau als
Erfolg an einer Stelle, an der nichts geschehen ist.

*Das ist F-04, vierte Ausprägung: eine Zahl im Auftrag, die ich nicht selbst gemessen habe.
**R11 hätte es gefangen** — ich habe den `population_command` hingeschrieben und nicht ausgeführt.*

**2. Der Vorher-Wert wird künftig vom Generator festgehalten, nicht von der Abnahme.**
`T1a/K-07` und `T2/K-06` sind beide unmessbar geworden, weil ihr Vorher-Wert nirgends stand.
**Zweite Wiederholung ⇒ R9 verlangt eine Barriere**, und der Vorschlag des Evaluators ist die
richtige: *eine Zeile in der Quittung, vor dem Bau.* **Kostet Sekunden, rettet ein P1.**

**3. Die Spur wird auf A korrigiert — und der Beleg ist eine Messung, keine Meinung.**
Der Evaluator hat es an T2 belegt: **neun Kriterien, sieben P1, zwei ihm zugewiesen — und der
Generator musste sieben Zusagen nachträglich erfinden**, weil die `grep`-Kriterien nichts
verriegelten. *„Eine Sache, die man so prüfen muss, ist keine ‚eine Ledger-Zeile'-Sache."*
**Er stuft nicht ein, ich schon — und er hat recht.** T3 ist ab jetzt **Spur A**.

*Alle drei Korrekturen stehen zuerst hier im Blatt und danach in Tafel und Ledger. Das ist die
Reihenfolge, die ich um 21:20 festgelegt habe, nachdem ich sie genau einmal andersherum gemacht
und damit einen halben Auftrag verursacht hatte.*
