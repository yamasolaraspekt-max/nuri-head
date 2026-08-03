# Z-07 — Das Dach nimmt die gezeichnete Kontur, wie die Decke

**Spur A** · **Heimat: ticket** · **Basis: HEAD beim Ziehen (N1 ist gebaut)** · *Geschnitten 03.08. 09:3x*

```yaml
auftrag:
  id: Z-07
  strang: hausplaner-3d
  status: gebaut   # GEBAUT 03.08. 23:1x (B14). K-01 0->2 · K-02 1->0 (Konstante WEG, nicht daneben) · Insel 1673->1676 pass / 0 fail · tsc 0. Mutationsprobe 6 Muster, danach 6/6 gefangen - M4 (Hinweis nie) war beim ersten Lauf BLIND: der Fuss-Hinweis fuers Dach hatte keine Zusage, die Decken-Zusage deckte nur ihren eigenen Melder. Ergaenzt als Z-07/K-04. Zwei bestehende Zusagen nachgezogen, beide von mir aus N1/Z-06: die 'nur das Dach behaelt den Umriss bedingungslos'-Zaehlung steht jetzt auf 0 statt 1 - das ist der Fortschritt, nicht die Schwaechung. OFFEN: L-01 Browserprobe.
  gegengelesen_von: pruefer
  gegengelesen_am: "2026-08-03"
  befund: "BAUBAR, KEIN sperrender Einwand. Alle Ausgangswerte an HEAD 14079a93 selbst nachgemessen: K-01 herkunftFuerNeuesDach 0 (Partner Decke inzwischen 2, Blatt nannte 1 - gedriftet, nicht falsch), K-02 HERKUNFT_NEUES_DACH 1 (freigabe.ts:97), gebaeudeUmriss in App 3, renderers/ 0 Treffer (Ausschluss-Begruendung bestaetigt), Vorbild-Zahl 68 in decke.test.ts 3x vorhanden. Validator 3 OK / 0 Fehlschlag. Eine NOTIZ ohne Sperrwirkung: der Partner-Ausgangswert 1 stammt vom 09:3x-Schnitt und ist heute 2 - der Bauende misst seine Basis ohnehin selbst (F-20/K-05). Status stellt der Planner (B14)."
```

## Warum — 10 Zeilen (B15)

**Die letzte Bounding-Box im Haus, gemessen an HEAD:**

```text
HausplanerApp.tsx:946   polygon: gebaeudeUmriss(), roofType: 'sattel' …     DAS DACH
HausplanerApp.tsx:979   polygon: ausKontur ? letzteKontur : gebaeudeUmriss()  die DECKE (Z-06)
HausplanerApp.tsx:949   ...HERKUNFT_NEUES_DACH                              statisch 'abgeleitet'
```

**Z-06 hat es für die Decke vorgemacht, N1 hat die Herkunfts-Domäne gebaut — dem Dach fehlt
dieselbe eine Zeile.** *Bei L-, T- und U-Grundrissen ist die Dach-Traufe heute still falsch,
genau wie die Decke vorher.* **Yamas B10 gilt: erbt das Dach die Kontur, erbt es den Status.**

## Die Entscheidung

```text
polygon:  ausKontur ? letzteKontur : gebaeudeUmriss()        wie Z-06, dieselbe Zeile
Herkunft: HERKUNFT_NEUES_DACH (Konstante)  ->  herkunftFuerNeuesDach(ausKontur)  (Funktion,
          in geometry/freigabe.ts NEBEN herkunftFuerNeueDecke - N1s Regel: die Entscheidung
          wohnt in der Domaene, der Klick-Handler RUFT sie)
Hinweis:  ohne Kontur derselbe Naeherungs-Vermerk wie bei der Decke, Wortlaut fuers Dach
```

**Z-08 wird NICHT auf Vorrat geschnitten:** *ob nach Z-07 überhaupt eine zweite Dach-Scheibe
nötig ist (Verschneidung, Gauben-Anschluss), entscheidet die Messung am gebauten Z-07 — nicht
die alte Nummerierung.* **B13-Geist: kein Blatt ohne gemessenen Bedarf.**

## Nahtstellen

```text
Hier wird geschrieben:
  app/HausplanerApp.tsx        die eine Zeile am ADD_ROOF + der Hinweis
  geometry/freigabe.ts         herkunftFuerNeuesDach(ausKontur); die Konstante faellt weg
  __tests__/…                  die Zusagen dazu

Hier bewusst NICHT:
  renderers/three-d/szene.ts   rendert roofs[] aus dem polygon - Kontur kommt automatisch an.
  Dachform/Neigung/Ueberstand  bleiben wie sie sind. Z-07 aendert die GRUNDFLAECHE, nicht
                               die Form.
  domain/validation.ts         N1 hat die Felder als Pflicht gebaut. Nichts nachzuziehen.
```

## Kriterien

```yaml
scope:
  dateien:
    - resources/planner/hausplaner/app/HausplanerApp.tsx
    - resources/planner/hausplaner/geometry/freigabe.ts
  population_command: "node scripts/zaehle.mjs resources/planner/hausplaner/app/HausplanerApp.tsx 'gebaeudeUmriss'"
  ausschluesse:
    - stelle: "renderers/three-d/szene.ts"
      grund: "Rendert roofs[] aus dem polygon. Die Kontur kommt automatisch an - gemessen: kein eigener Umriss-Aufruf dort."
      entschieden_von: planner
    - stelle: "Dachform, Neigung, Ueberstand"
      grund: "Z-07 aendert die Grundflaeche, nicht die Form. Eigene Scheibe, falls je gemessen noetig."
      entschieden_von: planner

kriterien:
  - id: K-01
    typ: presence
    kritikalitaet: P1
    aussage: "Das Dach kennt die Kontur - die Bounding-Box ist nur noch Rueckfall."
    pruefung:
      befehl: "node scripts/zaehle.mjs resources/planner/hausplaner/app/HausplanerApp.tsx 'herkunftFuerNeuesDach'"
      erwartet: "mindestens 1"
    ausgangswert: "0 (gemessen 03.08. 09:3x; Partner 'herkunftFuerNeueDecke' -> 1, die Messung ist nicht leer)"

  - id: K-02
    typ: absence
    kritikalitaet: P1
    aussage: "Die statische Herkunfts-Konstante ist WEG - nicht daneben."
    pruefung:
      befehl: "node scripts/zaehle.mjs resources/planner/hausplaner/geometry/freigabe.ts 'HERKUNFT_NEUES_DACH'"
      erwartet: "0"
    ausgangswert: "1 (Export in freigabe.ts:97)"
    gegenbeweis: |
      Bleibt die Konstante neben der Funktion stehen, gibt es zwei Antworten auf die Frage
      "woher kommt das Dach" - und der naechste Aufrufer nimmt die falsche. Dieselbe Klasse
      wie der doppelte Kommentar-Abzug (W-06) und PAKET_WERKZEUGE (W-05 K-10).

  - id: K-03
    typ: behavioural
    kritikalitaet: P1
    aussage: "B10 am Dach: mit Kontur manuell/bestaetigt, ohne Kontur abgeleitet/vorschlag - und es UEBERLEBT das Laden."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        herkunftFuerNeuesDach(true)  -> geometrieHerkunft 'manuell',    freigabe 'bestaetigt'
        herkunftFuerNeuesDach(false) -> geometrieHerkunft 'abgeleitet', freigabe 'vorschlag'
        Dach ohne Kontur anlegen, serialisieren, parsen -> beide Werte unveraendert.
        Die Zusage laeuft gegen die DOMAENEN-Funktion plus die N1-Zusage "die App RUFT" -
        wer die Entscheidung zurueck in den Handler zieht, faellt dort.
      erwartet: "drei Zusagen, die dritte ueber die Persistenz"

  - id: K-04
    typ: behavioural
    kritikalitaet: P1
    aussage: "Eine NICHT-rechteckige Kontur bekommt KEIN Dach - sie bekommt eine Absage, die der Nutzer liest."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        NEU GESCHNITTEN 03.08. 23:3x durch den Planner, nachdem der Evaluator die alte
        Fassung als UNERFUELLBAR belegt hat. Der alte Wortlaut verlangte "die L-Form
        bekommt ein L-Dach, 68 m2 statt 80". Das kann V1 nicht: `dachGeometrie.ts:87`
        wirft `DachGeometrieUngueltig` fuer jede Kontur, die nicht ihrer Bounding-Box
        entspricht - eine Schranke, die es VOR diesem Blatt schon gab und die ich beim
        Schneiden nicht gemessen habe. Das ist mein Fehler, keine Bauschuld (F-04:
        Machbarkeit behauptet statt gemessen).
        (a) L-Kontur zeichnen, Dach anlegen -> KEIN Dach-Objekt entsteht. Der Nutzer
            bekommt eine sichtbare, lesbare Absage mit Grund ("V1: nur rechteckige
            Grundrisse"). Gemessen an der Szene: roofs-Anzahl UNVERAENDERT.
        (b) Rechteck-Kontur zeichnen -> Dach entsteht und folgt der KONTUR, nicht der
            Bounding-Box aller Waende. DAS ist der gelieferte Wert dieses Blattes.
        (c) B10: bei (a) wird NICHTS geschrieben - kein Objekt, kein Status, und ganz
            besonders kein 'bestaetigt'. Ein Status auf einem Bauteil, das nicht
            existiert, ist die schaerfste Form des Herkunftsverlusts.
      erwartet: "(a) roofs unveraendert + sichtbare Absage · (b) Dach folgt der Kontur · (c) kein Schreibvorgang bei (a)"

  - id: K-04b
    typ: behavioural
    kritikalitaet: P1
    aussage: "Der Faenger im Renderer schweigt nicht mehr - ein Bestandsdach, das die Schranke wirft, wird SICHTBAR gemeldet."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        Der Evaluator hat die Ironie gemessen und sie gehoert in den Schnitt:
        `dachGeometrie.ts:87` sagt woertlich "sonst kein stilles Falschdach" - und
        `szene.ts:499/:545` fangen den Wurf mit `continue` bzw. `return`. Der Wurf
        verhindert ein stilles FALSCHES Dach, der Faenger macht daraus ein stilles
        FEHLENDES. Die Schranke funktioniert, sie wird nur nicht gehoert.
        Ein v2-Bestandsdokument kann ein solches Dach heute schon tragen (die Absage
        aus K-04 wirkt erst beim ANLEGEN). Darum: der Faenger meldet, statt zu schlucken.
      erwartet: "ein Dokument mit nicht-rechteckigem Dach zeigt einen lesbaren Hinweis statt einer leeren Stelle"

  - id: K-05
    typ: behavioural
    aussage: "Insel gruen - besonders die 15 Dach-Zusagen und die N1-Suite."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: "npm run test:hausplaner"
      erwartet: "0 fail. Basis misst der Bauende VOR dem Zug und benennt sie (F-20)."

  - id: K-06
    typ: behavioural
    aussage: "Mutationsprobe VOR den Zusagen."
    pruefung:
      typ: verfahren
      schritte: |
        Mindestens 5: Funktion gibt immer 'bestaetigt' · immer 'abgeleitet' · Handler trifft
        die Entscheidung wieder selbst · Konstante bleibt daneben · Kontur ignoriert (immer
        Box). Wie viele kommen durch?

  - id: L-01
    typ: presence
    kritikalitaet: P1
    aussage: "Browsertest - das Dach folgt der gezeichneten Kontur, und man sieht ihm die Herkunft an."
    pruefung:
      typ: browser
      schritte: |
        FLAECHE: objekt.blade (data-speichern-url:157) - NICHT studio, das speichert
        nicht (studio.blade:3). Siehe ANKER-BROWSER, "Die Persistenz-Flaeche".
        (a) L-Kontur zeichnen, Dach anlegen -> die Traufe folgt dem L, nicht der Box
        (b) Dach OHNE Kontur -> Naeherungs-Vermerk sichtbar (Wortlaut Dach)
        (c) speichern, NEU LADEN -> Status bleibt (B10; Kennzeichnung im Modell ist N2,
            hier zaehlt der DATENSTAND)
        KONTROLLE (B4): Rechteck-Grundriss - Kontur- und Box-Dach identisch.
        Viewports 1440 und 1024.

  - id: L-01-anker
    typ: verweis
    quelle: docs/auftraege/ANKER-BROWSER.md
```

## Kantenliste (B9)

```text
1  Konstante und Funktion nebeneinander - zwei Wahrheiten.          -> K-02
2  Entscheidung wandert zurueck in den Klick-Handler.               -> K-03 (N1-Zusage)
3  Dach nimmt die Kontur, Decke und Dach driften bei Aenderung.
   OHNE ZUSAGE, mit Grund: beide lesen DIESELBE letzteKontur zum Anlegezeitpunkt; ein
   spaeteres Auseinanderlaufen ist die Bestaetigungs-Frage aus N3 (Fingerabdruck), nicht
   diese Scheibe.
4  Der Naeherungs-Vermerk fehlt am Dach, obwohl die Decke ihn hat.  -> L-01(b)
```

## Rückweg und Entdeckung

**Rückweg:** zwei Dateien, kein Schema — N1 hat die Felder schon. **Entdeckung:** K-04s rote
Gegenprobe. *Ein Box-Dach auf einem L-Haus sieht fertig aus — genau deshalb ist die Fläche die
Zusage, nicht der Eindruck.*
