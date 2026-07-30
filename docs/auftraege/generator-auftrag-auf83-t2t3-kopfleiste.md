> # ⛔ HISTORISCH — ersetzt durch die Einzelblätter T2 und T3
>
> **Planner, 30.07.2026, 09:05. Auf `PB-008` des unabhängigen Prüfers, nachgemessen und bestätigt.**
>
> **Dieses Blatt führt `T1a` als offenen Schritt.** Gemessen: **beide Messungen, auf denen es
> steht, existieren nicht mehr** — weil T1a gebaut ist:
>
> ```text
> min-height: calc(100vh - 46px)   →  0 Treffer in beiden Hausplaner-Blades
> innerWidth - 220 - 268           →  0 Treffer in HausplanerApp.tsx
> stattdessen:  HausplanerApp.tsx:370  useGemesseneBreite(inhaltRef)
> ```
>
> **Verbindlich sind die Einzelblätter:** `generator-auftrag-auf83-t2-zweite-navigation.md` ·
> `generator-auftrag-auf83-t3-kopfleiste-arbeitszeile.md` ·
> `generator-auftrag-auf83-t3-n1-zeile-eins-verschlanken.md`.
>
> *Der Prüfer hat es so formuliert: „das Papier hat vorgeschlagen, was heute im Code steht" — es ist
> nicht falsch, es ist **eingelöst**. Ein eingelöstes Papier als offenen Schritt zu führen, schickt
> jemanden los, etwas zu bauen, das steht.*


---

# AUF-83-T2T3 — Aus drei Kopfleisten wird eine

*Planner, 29.07.2026, 01:10 CEST. Erster baubarer Teil von AUF-83 (Yamas Auftrag zum Studio-Rahmen).
Grundlage: `docs/planner/bestandsaufnahme-studio-rahmen-2026-07-29.md`.*

> **GESPERRT bis zur Entwurfsfreigabe.** Yama hat am 29.07. um 01:12 verlangt, die Änderung
> **vorher als Entwurf zu sehen**: *„kannst du die änderung als entwurflayout hier zeigen damit ich
> es freigeben kann bevor es umgesetzt wird“*. Der Entwurf liegt als
> `docs/planner/entwurf-studio-kopfleiste-2026-07-29.html`.
>
> **Dieses Blatt wird erst `aktiv`, wenn er ihn freigegeben hat** — und zwar in der Marke auf der
> Tafel, nicht in diesem Fließtext. *Eine Sperre im Fließtext ist keine Sperre; das haben wir am
> 27.07. zweimal in drei Stunden gelernt.*

```yaml
auftrag:
  id: AUF-83-T2T3
  status: gesperrt
  sperrgrund: "wartet auf Yamas Freigabe des Entwurfs (entwurf-studio-kopfleiste-2026-07-29.html)"
  spur: B
  heimat: ticket
  ziel: >
    Der Studio-Bildschirm traegt EINE Kopfleiste statt dreier. Der Testflaechen-Hinweis und die
    Hausplaner-Bezeichnung erscheinen je genau einmal, und der Erklaertext des Expertenmodus
    kostet keine eigene Zeile mehr.
  nicht_ziel: >
    KEIN @extends, KEIN Blade-Layoutwechsel, KEINE Aenderung an Routen, Rechten oder Auth —
    das ist T1 und gehoert Yama. Der Zurueck-Link in der Blade BLEIBT, solange es keine
    Ticket-Navigation gibt. KEINE Aenderung an HausplanerApp.tsx (dort laufen Scheibe 7 und
    AUF-48). KEINE Aenderung an der Breiten- oder Hoehenrechnung — das ist T1a/T4.

scope:
  population_command: >
    grep -c 'Testfläche' resources/views/admin/hausplaner/studio.blade.php &&
    grep -rn 'hp-title\|hp-scratch' resources/views/admin/hausplaner/
  population_at_writing: >
    Testflaechen-Hinweis 2x (Blade + Insel), Hausplaner-Bezeichnung 3x (Blade hp-title,
    Blade-Skeleton h1, Insel), Erklaerzeile 1x, "Zur gefuehrten Planung" als eigene Zeile 1x.
    Messung des Planners, ausdruecklich KEINE Bedingung.
  pfade:
    - resources/views/admin/hausplaner/studio.blade.php
    - resources/planner/hausplaner/app/HausplanerStudio.tsx
  ausschluesse:
    - stelle: "resources/views/admin/hausplaner/objekt.blade.php"
      grund: >
        Sie traegt weder hp-scratch noch hp-bar; ihre Doppelung ist nur das Skeleton-h1, und das
        ist der Ladeplatzhalter. Sie anzufassen waere Beifang.
      entschieden_von: planner

kriterien:
  - id: K-01
    aussage: "Der Testflaechen-Hinweis steht genau einmal, und zwar in der Insel."
    typ: absence
    kritikalitaet: P1
    pruefung:
      befehl: "grep -n 'Testfläche' resources/views/admin/hausplaner/studio.blade.php"
      erwartet: >
        Kein Treffer mehr in der hp-bar (Z34). Treffer im <title> und in den Kommentaren duerfen
        bleiben — sie sind keine Anzeige.
    beleg: grepausgabe
    partner: >
      presence-Partner nach R2: die Insel-Anzeige muss WEITER da sein.
      `npm run test:hausplaner -- --filter=speicherAnzeige` bleibt gruen (er prueft in
      __tests__/speicherAnzeige.test.ts:29 den Wortlaut).
    begruendung: >
      Der zu entfernende ist der BLADE-Text, nicht der Insel-Text. Der Insel-Text ist an das
      fehlende data-speichern-url gekoppelt, ist testverriegelt und sagt auf der Objekt-Flaeche
      automatisch nichts. Die Blade-Zeichenkette steht immer da, egal was gilt.

  - id: K-02
    aussage: "Die Hausplaner-Bezeichnung erscheint fuer den Nutzer genau einmal."
    typ: behavioural
    kritikalitaet: P1
    pruefung:
      typ: visuell
      schritte: "Studio oeffnen, nach dem Mount den sichtbaren Text erfassen"
      erwartet: >
        Genau eine sichtbare Hausplaner-Bezeichnung. Das <h1> im #hausplaner-root-Skeleton
        ist der Ladeplatzhalter und MUSS bleiben — zu belegen ist, dass es nach dem Mount
        NICHT MEHR im DOM steht.
    beleg: DOM-Auszug vor und nach dem Mount
    ausgefuehrt_von: evaluator
    achtung: >
      Hier NICHT blind loeschen. Bleibt das h1 beim Fehlschlag der Insel stehen, ist es genau der
      Platzhalter, der gebraucht wird. Erst messen, ob der Mount es ersetzt, DANN entscheiden.
      Ersetzt der Mount es, ist nichts zu tun und K-02 ist mit dem Beleg erfuellt.

  - id: K-03
    aussage: "Der Erklaertext des Expertenmodus kostet keine eigene Zeile mehr."
    typ: behavioural
    kritikalitaet: P2
    pruefung:
      befehl: "grep -n 'hp-experte-hinweis' resources/planner/hausplaner/app/HausplanerStudio.tsx"
      erwartet: >
        Der Text ist erhalten, aber nicht mehr als dauerhafte Zeile ueber der Buehne — zulaessig
        sind Titel-Attribut an der Modus-Umschaltung, Popover oder Einblendung beim ersten
        Betreten. Die Hoehe der Zeile faellt weg.
    beleg: vorher/nachher-Hoehe der Buehne in px
    grenze: >
      Der Text wird NICHT geloescht. Er beantwortet eine Frage, die man einmal hat — er soll
      auffindbar bleiben, nur nicht dauerhaft Hoehe kosten.

  - id: K-04
    aussage: "'Zur gefuehrten Planung' ist keine eigene breite Zeile mehr."
    typ: behavioural
    kritikalitaet: P2
    pruefung:
      typ: visuell
      schritte: "Expertenmodus oeffnen"
      erwartet: "Der Weg zurueck in die gefuehrte Planung ist erreichbar, aber Teil der Kopfleiste."
    beleg: vorher/nachher-Bildschirmfoto

  - id: K-05
    aussage: "Der Zeichenbereich gewinnt messbar Hoehe."
    typ: coverage
    kritikalitaet: P1
    pruefung:
      typ: visuell
      schritte: "1440 px, Expertenmodus, getBoundingClientRect der Buehne vorher und nachher"
      erwartet: >
        Die Buehnenhoehe waechst. UM WIEVIEL, WIRD GEMESSEN UND BERICHTET — es ist kein Sollwert.
        Waechst sie NICHT, ist das der eigentliche Befund und geht an den Planner zurueck.
    beleg: zwei getBoundingClientRect-Ausgaben
    begruendung: >
      Das ist die Zusage, die den Auftrag ehrlich macht. Yamas Ziel ist Platz, nicht Ordnung.
      Eine Kopfleiste zusammenzulegen, die keine Hoehe spart, hat das Ziel verfehlt — auch wenn
      sie danach aufgeraeumter aussieht.

  - id: K-06
    aussage: "Der Zurueck-Weg aus dem Studio bleibt erreichbar."
    typ: presence
    kritikalitaet: P1
    pruefung:
      befehl: "grep -n 'url()->previous\\|Zurück' resources/views/admin/hausplaner/studio.blade.php"
      erwartet: "Der Link ist da."
    beleg: grepausgabe
    begruendung: >
      Solange T1 nicht entschieden ist, gibt es KEINE Ticket-Navigation. Faellt der Zurueck-Link
      mit der hp-bar, ist die Studio-Flaeche eine Sackgasse. Das ist der Grund, warum dieser
      Auftrag die hp-bar AUSDUENNT und nicht ENTFERNT.

  - id: K-07
    aussage: "Nichts ausserhalb des Scopes ist beruehrt, Gates ohne Regression."
    typ: absence
    kritikalitaet: P1
    pruefung:
      befehl: "git show --name-only --pretty=format: HEAD && npm run tsc:hausplaner && npm run schema:hausplaner:check && npm run test:hausplaner && npm run test:hausplaner:dom"
      erwartet: >
        Genau die zwei Pfade aus scope.pfade (plus public/* aus dem Bau).
        Gates 0/0/0/0, Insel >= 1323.
    beleg: dateiliste + testzaehler

  - id: K-08
    aussage: "Geerbte Zusagen, die den alten Aufbau lesen, sind mitgezogen."
    typ: coverage
    kritikalitaet: P1
    pruefung:
      befehl: "grep -rn 'hp-experte-hinweis\\|hp-title\\|hp-scratch\\|hp-bar' resources/planner/hausplaner/__tests__/"
      erwartet: "Kein Test prueft noch die alte Gestalt; wo doch, ist er auf die Wirkung umgestellt."
    beleg: grepausgabe
    begruendung: >
      Fuenfmal in AUF-38 hat eine geerbte Zusage den alten Zustand festgehalten und ist beim Umbau
      rot gegangen, ohne dass ein Fehler vorlag. Das gehoert seit Scheibe 5 in den Auftrag und
      nicht in die Ueberraschung.

selbstnachweis:
  quittung_zuerst: "Readiness-Quittung nach §2, bevor eine Zeile entsteht."
  gegenprobe: >
    K-01: den Blade-Text wieder einsetzen ⇒ die Zusage muss rot werden. Faellt sie nicht, prueft
    sie die Gestalt statt der Wirkung und ist wertlos (R2).
  sichtprobe: >
    Diese Aenderung ist REIN VISUELL — kein Gate faengt sie. Vorher/nachher in drei Viewports
    (1440/1024/375), und bei 375 px BITTE HINSEHEN: die Layout-Inventur vom 25.07. hat dort einen
    Ueberlauf von 283 px gemessen (Befund B5, inzwischen als AUF-46 abgearbeitet). Wenn er
    zurueck ist, ist das ein Befund.
```

---

## Warum dieser Auftrag ohne Yamas Entscheidung läuft

Yamas Auftrag beginnt mit *„Ticket-Navigation beibehalten"*. **Die Messung hat gezeigt: es gibt
keine** — beide Blades haben `0× @extends`. Das macht Punkt 1 zu einem Herstellungsauftrag mit
Auth, Rollen und Routing, und der gehört Yama.

**Alles andere hängt daran nicht.** Dieser Auftrag entfernt die Doppelungen und legt die Kopfzeilen
zusammen, **ohne die Blade-Leiste ganz zu entfernen** — sie trägt bis auf Weiteres den einzigen
Rückweg aus dem Studio. Kommt T1, fällt sie mit; bis dahin dünnt sie aus.

*Ich hatte in der Bestandsaufnahme geschrieben, T2 hänge an T1. Das war zu eng gedacht: es hängt
nur der Wegfall der Leiste an T1, nicht das Entfernen der Doppelung.*

## Die Reihenfolge, in der das steht

1. **AUF-38 Scheibe 5** (`ConfigWizard`) — läuft, wird nicht unterbrochen (§13).
2. **Dieses Blatt** — `HausplanerStudio.tsx` ist seit 00:45 abgenommen und damit frei.
3. **T1a / T4** — die Insel nimmt ihre Maße vom Behälter statt vom Fenster. Das ist zugleich der
   erste Zerlegungsschritt von **AUF-48**, siehe `t1-entscheidungsgrundlage-ticket-shell-2026-07-29.md`.
4. **T1b** — der Blade-Umbau. **Bei Yama.**
5. **T5** — Panels als Overlay, Escape-Stapel, Zustand in `localStorage`.
