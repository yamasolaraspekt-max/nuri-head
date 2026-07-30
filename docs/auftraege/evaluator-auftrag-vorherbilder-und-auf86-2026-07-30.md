# EVALUATOR — Zwei Messungen, die JETZT fällig sind (30.07.2026, 06:35 CEST)

*Planner. **Beide Teile sind eilig, und zwar aus demselben Grund:** der Generator baut T3 gerade.
Landet sein Commit, bevor Teil A gemessen ist, ist K-01 **nicht mehr prüfbar** — das wäre die
**dritte** Ausprägung derselben Sache an zwei Tagen (T1a/K-07, T2/K-06).*

> **Reihenfolge ist hier keine Empfehlung: A vor B.** B kann warten, A nicht.

```yaml
auftrag:
  id: EVAL-2026-07-30-A+B
  status: aktiv
  spur: A
  heimat: ticket
  rolle: evaluator
  schreibrecht: >
    NUR `docs/`. Kein Produktivcode, kein Push, kein Tor 2. Bildschirmfotos nach
    `docs/sichtproben/2026-07-30/`.
  nicht_ziel: >
    KEINE Abnahme von T3 — der Auftrag ist noch im Bau. Hier wird nur der Ausgangsstand
    festgehalten und ein gemeldeter Befund vermessen.
```

---

## TEIL A — der Vorher-Stand von T3, bevor er verschwindet

**Was T3 verändern wird** (Blatt `9a8c16cc`): die Kopfleiste bekommt Projektname, Objektname und
den Übernehmen-Knopf; `Import` in der Arbeitsbereich-Zeile wird ausgegraut; ein `⌘K`-Einstieg
kommt dazu. **Es bleiben genau drei Zeilen.**

```yaml
kriterien:
  - id: A-01
    aussage: "Der Ausgangsstand ist in drei Viewports festgehalten."
    typ: presence
    kritikalitaet: P1
    pruefung:
      typ: visuell
      schritte: >
        Echte Ticket-Shell, `/admin/hausplaner/studio`, Expertenmodus geklickt.
        Viewports 1440 / 1024 / 375. Je Viewport EIN Bild der oberen drei Zeilen.
      erwartet: >
        Sechs Bilder gesamt: drei vom Studio, drei von `/admin/hausplaner/objekt/...`
        (dort stehen Objektname und Uebernehmen-Knopf HEUTE noch).
    beleg: Dateipfade der Bilder + je Bild ein Satz, was darauf zu sehen ist

  - id: A-02
    aussage: "Die Zeilenhoehen sind als ZAHLEN festgehalten, nicht nur als Bild."
    typ: coverage
    kritikalitaet: P1
    pruefung:
      typ: visuell
      schritte: >
        Bei 1440 px `getBoundingClientRect()` je Zeile:
        Zeile 1 (Dokument, `HausplanerApp.tsx:1184`) · Zeile 2 (Arbeitsbereiche, `:1256`) ·
        Zeile 3 (Werkzeugzeile, `:1270`) · dazu die Buehne und `#hausplaner-root`.
      erwartet: "fuenf Rechtecke, roh ausgegeben"
    beleg: rohausgabe
    begruendung: >
      **Ein Bild zeigt, dass es gleich AUSSIEHT. Eine Zahl zeigt, dass es gleich IST.**
      Der Generator hat den K-08-Wert schon festgehalten (Leinwand 595×538, Wurzel 1083×788
      bei 1440×900) — **hier fehlen die drei Zeilen einzeln**, und genau die veraendert T3.

  - id: A-03
    aussage: "Die vier AUF-70-Zusagen sind VOR dem Bau als gruen belegt."
    typ: presence
    kritikalitaet: P1
    pruefung:
      befehl: "npm run test:hausplaner -- --filter=eineWerkzeugzeile"
      erwartet: "gruen, mit Zaehler"
    beleg: testausgabe
    begruendung: >
      Sie sind die **Gegenprobe** von T3/K-01: bleiben sie gruen, hat der Bau AUF-70 nicht
      zurueckgedreht. **Ein Gruen nach dem Bau beweist nichts, wenn niemand das Gruen davor
      gesehen hat.**
```

---

## TEIL B — AUF-86: die Leinwand ragt 23 px über ihren Behälter

**Gemeldet vom Generator** (30.07., 06:19, Chrome 1440×900, echte Shell, Expertenmodus):

```text
#hausplaner-root canvas[0]   unten 899
#hausplaner-root             unten 876     ⇒ 23 px Ueberstand, stoesst an die Fensterkante
```

**Er hat es nicht angefasst und gemeldet** — ausserhalb seines Umfangs. *Das ist die richtige
Reihenfolge, und sie kostet ihn Zeit; deshalb steht sie hier.*

```yaml
kriterien:
  - id: B-01
    aussage: "Der Ueberstand ist reproduzierbar — oder er war ein Messartefakt."
    typ: behavioural
    kritikalitaet: P1
    pruefung:
      typ: visuell
      schritte: >
        Dieselbe Messung bei 1440×900, 1280×800, 1024×768, 375×667. Je Viewport
        `canvas[0]` und `#hausplaner-root` als Rechteck.
      erwartet: >
        Die Zahlen, roh. **Kein Sollwert.** Tritt er nur bei einer Fensterhoehe auf, ist das
        der eigentliche Befund.
    beleg: rohausgabe je Viewport

  - id: B-02
    aussage: "Die Ursache ist benannt — Hoehe oder Breite, Insel oder Shell."
    typ: coverage
    kritikalitaet: P1
    pruefung:
      typ: visuell
      erwartet: >
        Eine der folgenden Aussagen, mit Beleg:
        (a) die Buehnenhoehe rechnet zu grosszuegig (`buehnenHoehe.ts`),
        (b) die Ticket-Shell gibt weniger Hoehe als die Insel annimmt (`.main-content-scroll`),
        (c) die Leinwand ignoriert ihren Behaelter (Konva/`stage`),
        (d) etwas anderes — dann benannt.
    beleg: die gemessenen Rechtecke der Kette Shell → Wurzel → Buehne → Leinwand

  - id: B-03
    aussage: "Seit wann er da ist, ist gemessen — nicht vermutet."
    typ: coverage
    kritikalitaet: P2
    pruefung:
      typ: visuell
      schritte: >
        Dieselbe Messung auf `a14abb53` (T1b) und auf `97a2e2a4` (T1a) —
        `git archive` in ein Verzeichnis, bauen, messen. **Nicht auschecken**, der Baum ist geteilt.
      erwartet: "drei Zahlen, drei Staende"
    beleg: rohausgabe je Stand
    grenze: >
      **Wenn das zu teuer wird, brich ab und melde es.** B-03 ist P2; B-01 und B-02 sind der Kern.
      *Eine abgebrochene Messung, die gemeldet wird, ist besser als eine, die drei Stunden kostet.*
```

---

## Was danach mit dem Ergebnis geschieht

**Teil A** wird der Prüfstand für die Abnahme von T3 — ohne ihn ist K-01 nicht messbar.

**Teil B** entscheidet, ob AUF-86 ein eigener Auftrag wird oder in T5 aufgeht. **Und es entscheidet,
ob K-08 von T3 überhaupt aussagekräftig ist:** *ein Höhengewinn, der aus einem Überstand kommt,
ist keiner.* Deshalb steht diese Messung vor der Abnahme und nicht danach.

**Ballbesitz nach der Meldung: Planner.**
