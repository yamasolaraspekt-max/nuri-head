# AUF-83-T2 — Die zweite und die dritte Navigation fallen

*Planner, 29.07.2026, 10:05 CEST. Dritter Schritt von AUF-83. Grundlage:
`docs/planner/entwurf-studio-in-ticket-shell-2026-07-29.html`, von Yama am 29.07. um 08:20
freigegeben.*

> **GESPERRT, bis `AUF-83-T1b` gebaut ist** (nicht: abgenommen). Grund: solange die
> Ticket-Navigation nicht da ist, ist der Zurück-Link der Blade der **einzige** Weg aus dem Studio.
> Ihn vorher zu entfernen macht die Fläche zur Sackgasse. *Die Sperre endet mit dem Bau, nicht mit
> der Abnahme — geht T1b rot, wird T2 mit nachgebessert, das kostet weniger als Stillstand.*

```yaml
auftrag:
  id: AUF-83-T2
  status: ruht            # entsperrt: T1b ist gebaut (a14abb53) und abgenommen
   # PB-B2, 01.08.2026 - Planner: `ruht` heisst, der Zustand ist NICHT nachgemessen.
   # Wer das Blatt zieht, misst zuerst. S-01 erwartet genau EIN aktives Blatt.
  nachtrag: "29.07. 21:20 — Umfang um objekt.blade erweitert (K-01b); der alte Ausschluss war falsch begruendet"
  spur: B
  heimat: ticket
  ziel: >
    Der Studio-Bildschirm traegt genau EINE Navigation: die von Ticket. Die Blade-Leiste und die
    Studio-eigene Navigation entfallen, die Hausplaner-Marke entfaellt, der Testflaechen-Hinweis
    erscheint einmal.
  nicht_ziel: >
    KEINE neue Kopfleiste bauen — das ist T3. KEIN Eingriff in die Ticket-Shell.
    KEINE Aenderung an HausplanerApp.tsx (Scheibe 7 gesperrt, 78 offen).
    Die Fachplaner-DATEN (`FACH`, `PROJ` in studioDaten.ts) werden NICHT geloescht — nur ihre
    Darstellung als zweiter Navigationsbaum.

scope:
  population_command: >
    grep -c 'hp-navi' resources/planner/hausplaner/app/HausplanerStudio.tsx
    resources/planner/hausplaner/hausplaner.css &&
    grep -n 'hp-bar\|hp-title\|hp-scratch' resources/views/admin/hausplaner/studio.blade.php
  population_at_writing: >
    11 `hp-navi`-Vorkommen in HausplanerStudio.tsx, 9 in hausplaner.css.
    Blade: hp-bar (Z31), hp-title (Z32), Zurueck-Link (Z33), hp-scratch (Z34).
    Marke: HausplanerStudio.tsx ~Z116 (`Hausplaner` + `hp-marke-zusatz`).
    Messung des Planners, KEINE Bedingung.
  pfade:
    - resources/views/admin/hausplaner/studio.blade.php
    - resources/planner/hausplaner/app/HausplanerStudio.tsx
    - resources/planner/hausplaner/hausplaner.css
  ausschluesse:
    - stelle: "studioDaten.ts — FACH und PROJ"
      grund: >
        Die Daten bleiben. `FACH` sind die Fachplaner-Einstiege; sie wandern mit T3 in die
        Arbeitszeile, weil die Fachplaner die ARBEITSBEREICHE sind (arbeitsbereiche.ts fuehrt sie
        bereits: Import, Architektur, Bauphysik, Heizung, Elektro/PV). Hier faellt nur die
        Darstellung als Baum, nicht der Inhalt.
      entschieden_von: planner
    # ---- 29.07., 21:20: DIESER AUSSCHLUSS IST AUFGEHOBEN ----
    # Der urspruengliche Text lautete: "objekt.blade.php — sie traegt weder hp-bar noch
    # hp-scratch. Sie anzufassen waere Beifang."
    # DER GENERATOR HAT IHN IN DER QUITTUNG WIDERLEGT, und er hatte recht:
    #   grep -c 'hp-bar' objekt.blade.php  ->  4   (CSS Z35-37, Element Z78)
    # Nur der Teil ueber hp-scratch stimmte. Nach R11 waere hier ein grep faellig gewesen,
    # bevor das Blatt liegt.
    - stelle: "objekt.blade.php — Objektname, Adresse und der W-A-Uebernehmen-Knopf"
      grund: >
        NUR DIESE bleiben stehen. Sie sind echter, einzigartiger Inhalt an Fachlogik, kein
        doppelter Kopf — sie wandern mit T3 in die Kopfleiste, dort wo ohnehin Projekt und
        Geschoss stehen.
      entschieden_von: planner

kriterien:
  - id: K-01
    aussage: "Der Studio-Bildschirm traegt genau eine Navigation."
    typ: absence
    kritikalitaet: P1
    pruefung:
      befehl: "grep -c 'hp-navi' resources/planner/hausplaner/app/HausplanerStudio.tsx"
      erwartet: "0"
    beleg: grepausgabe vorher/nachher
    partner: >
      presence-Partner nach R2: die TICKET-Navigation muss im DOM sein und `Hausplaner` als aktiv
      markieren — sonst hat man nicht aufgeraeumt, sondern die Navigation entfernt.

  - id: K-01b
    aussage: "Auch die Objektseite traegt den doppelten Kopf nicht mehr."
    typ: absence
    kritikalitaet: P1
    nachgetragen: "29.07., 21:20 — Umfangserweiterung nach dem Befund des Generators"
    pruefung:
      befehl: "grep -n 'hp-title\\|url()->previous' resources/views/admin/hausplaner/objekt.blade.php"
      erwartet: >
        Kein Treffer mehr. `hp-title` ("Hausplaner") und der Zurueck-Link sind nach T1b in BEIDEN
        Blades ueberfluessig — die Ticket-Navigation erledigt beides.
    beleg: grepausgabe vorher/nachher
    grenze: >
      Objektname, Adresse und der Uebernehmen-Knopf mit Staleness-Pille BLEIBEN. Sie sind kein
      doppelter Kopf, sondern Inhalt.
    begruendung: >
      Beide Seiten erben seit T1b dieselbe Shell. Nur eine zu raeumen hinterliesse zwei
      Hausplaner-Flaechen mit verschiedenem Kopf — genau die zweite Wahrheit, gegen die dieser
      ganze Auftrag laeuft. **Beifang waere gewesen, in objekt.blade etwas ANDERES anzufassen;
      denselben Fehler an beiden Orten zu beheben ist keiner.**

  - id: K-02
    aussage: "Der Testflaechen-Hinweis erscheint fuer den Nutzer genau einmal."
    typ: absence
    kritikalitaet: P1
    pruefung:
      befehl: "grep -n 'Testfläche' resources/views/admin/hausplaner/studio.blade.php"
      erwartet: >
        Kein Treffer mehr in der sichtbaren Leiste. Treffer im <title> und in Kommentaren duerfen
        bleiben — sie sind keine Anzeige.
    beleg: grepausgabe + DOM-Auszug
    partner: >
      presence-Partner: `npm run test:hausplaner -- --filter=speicherAnzeige` bleibt gruen. Der
      Insel-Hinweis ist an das fehlende `data-speichern-url` gekoppelt und testverriegelt; die
      Blade-Zeichenkette steht immer da, egal was gilt. **Der zu entfernende ist die Blade.**

  - id: K-03
    aussage: "Die Hausplaner-Marke ist fort."
    typ: absence
    kritikalitaet: P2
    pruefung:
      befehl: "grep -n 'hp-marke\\|hp-title' resources/planner/hausplaner/app/HausplanerStudio.tsx resources/views/admin/hausplaner/studio.blade.php"
      erwartet: "kein Treffer"
    beleg: grepausgabe
    begruendung: >
      Yama: *„ich brauche kein logo fuer hausplaner oben.“* Der Grund ist nicht Geschmack:
      `sidebar.blade.php:570` markiert den Bereich bereits ueber `active_routes`. **Eine zweite
      Marke daneben ist Wiederholung, keine Orientierung.**

  - id: K-04
    aussage: "Der Erklaertext des Expertenmodus kostet keine eigene Zeile mehr."
    typ: behavioural
    kritikalitaet: P2
    pruefung:
      befehl: "grep -n 'hp-experte-hinweis\\|hp-experte-leiste' resources/planner/hausplaner/app/HausplanerStudio.tsx"
      erwartet: >
        Der Text ist ERHALTEN, aber nicht mehr als dauerhafte Zeile: Titel-Attribut am
        Modusschalter, Popover oder Einblendung beim ersten Betreten. Die Zeilenhoehe faellt weg.
    beleg: vorher/nachher-Hoehe der Buehne in px
    grenze: "Der Text wird NICHT geloescht. Er beantwortet eine Frage, die man einmal hat."

  - id: K-05
    aussage: "Der Weg in die gefuehrte Planung bleibt erreichbar."
    typ: presence
    kritikalitaet: P1
    pruefung:
      typ: visuell
      schritte: "Expertenmodus oeffnen"
      erwartet: >
        Der Wechsel zurueck ist da — als Modusschalter, nicht als eigene breite Zeile.
        *Modus wechseln ist Modus wechseln; kein zweiter Mechanismus daneben.*
    beleg: Bildschirmfoto
    ausgefuehrt_von: evaluator

  - id: K-06
    aussage: "Der Zeichenbereich gewinnt messbar Hoehe."
    typ: coverage
    kritikalitaet: P1
    pruefung:
      typ: visuell
      schritte: "1440 px, Expertenmodus, getBoundingClientRect der Buehne vorher und nachher"
      erwartet: >
        Die Buehnenhoehe waechst. UM WIEVIEL, WIRD GEMESSEN UND BERICHTET — es ist kein Sollwert.
        Waechst sie NICHT, ist das der Befund und geht an den Planner zurueck.
    beleg: zwei getBoundingClientRect-Ausgaben
    ausgefuehrt_von: evaluator
    begruendung: >
      Yamas Ziel ist Platz, nicht Ordnung. Eine Navigation zu entfernen, die keine Hoehe spart,
      hat das Ziel verfehlt — auch wenn es danach aufgeraeumter aussieht.

  - id: K-07
    aussage: "Geerbte Zusagen sind mitgezogen — vollstaendig, nicht nach Muster."
    typ: coverage
    kritikalitaet: P1
    pruefung:
      befehl: "grep -rl 'HausplanerStudio' resources/planner/hausplaner/__tests__/"
      erwartet: >
        Die LISTE dieser Dateien steht in der Quittung, und jede ist angesehen. NICHT die Zahl der
        Treffer eines Musters.
    beleg: Dateiliste + je Datei ein Satz, ob sie betroffen ist
    barriere: >
      Das ist die R9-Barriere vom 29.07., 10:00 — deine eigene Antwort auf die dritte Auspraegung
      derselben Fehlerklasse. Bei `HausplanerApp.tsx` waren es 22 Dateien; hier ist die Menge
      kleiner, das Verfahren dasselbe.

  - id: K-08
    aussage: "Nichts ausserhalb des Scopes beruehrt, Gates ohne Regression."
    typ: absence
    kritikalitaet: P1
    pruefung:
      befehl: "git show --name-only --pretty=format: HEAD && npm run tsc:hausplaner && npm run schema:hausplaner:check && npm run test:hausplaner && npm run test:hausplaner:dom"
      erwartet: "genau die drei Pfade aus scope (plus public/* aus dem Bau); Gates 0/0/0/0"
    beleg: dateiliste + testzaehler

selbstnachweis:
  quittung_zuerst: "Readiness-Quittung nach §2, mit der Dateiliste aus K-07."
  gegenprobe: >
    K-01: die `hp-navi`-Navigation wieder einsetzen ⇒ die Zusage muss rot werden.
    K-02: den Blade-Text wieder einsetzen ⇒ rot. Faellt sie nicht, prueft sie die Gestalt statt
    der Wirkung.
  sichtprobe: >
    REIN VISUELL — kein Gate faengt das. Vorher/nachher in drei Viewports (1440/1024/375).
    Bei 375 px bitte hinsehen: die Layout-Inventur hat dort einmal 283 px Ueberlauf gemessen
    (Befund B5, als AUF-46 behoben). Ist er zurueck, ist das ein Befund.
```

---

## Warum drei Navigationen entstanden sind

Nicht durch Nachlässigkeit, sondern weil **drei Schichten unabhängig voneinander eine Navigation
gezeichnet haben**: die Blade eine Leiste, die Insel eine Marke, und `HausplanerStudio` einen
eigenen Baum mit *Projekt* und *Fachplaner*. Jede für sich ist begründbar. Zusammen sind sie das,
was Yamas Auftrag in Punkt 1 verbietet — **eine zweite App-Shell.**

**Die Fachplaner sind sachlich falsch einsortiert**, nicht nur zu viel: `arbeitsbereiche.ts` führt
sie längst als Arbeitsbereiche (Import & Nachzeichnen · Architektur · Bauphysik · Heizung ·
Elektro · PV). Sie in einem Baum daneben zu führen heißt, dieselbe Sache zweimal zu benennen —
und eine zweite Benennung ist eine zweite Wahrheit.

## Reihenfolge

1. ~~T1a~~ — gebaut (`97a2e2a4`), beim Evaluator.
2. **T1b** — Blades an die Shell; die Ticket-Navigation erscheint.
3. **Dieses Blatt (T2)** — die zweite und dritte Navigation fallen.
4. **T3** — Kopfleiste und Arbeitszeile; die 13-teilige Geschosszeile verschwindet.
5. **T5** — Eigenschaften-Panel klappbar. *Die Vorbedingung liegt seit T1a: der Beobachter hängt
   an den einzelnen Schienen.*
