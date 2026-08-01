# Z-05 — Mehrpunkt-Kontur zeichnen, mit Prüfung beim Schließen

**Spur A** · **Heimat: ticket** · **Basis: HEAD beim Ziehen** · *Geschnitten 01.08. 12:1x*

```yaml
auftrag:
  id: Z-05
  status: gebaut   # 264ab9dc, 01.08. 19:09 - wartet auf das Votum des Evaluators. L-01 ist nach Z-05-N1 verschoben.
```

## Warum

**Heute kann man nur Wände zeichnen.** Wer eine Fläche braucht — für die Decke, später für das Dach —
bekommt die **Bounding-Box aller Wände**. Bei einem Rechteck stimmt das zufällig; bei L-, T- oder
U-Form ist es falsch, und zwar **still**: die Decke erscheint, sie ist nur zu groß.

**Z-05 liefert die Kontur. Z-06 setzt sie ein.** *Diese Scheibe baut das Werkzeug, nicht die Decke.*

## Bestand — gemessen 01.08. 12:1x

```text
Werkzeugtypen        'auswahl' | 'wand' | 'fenster' | 'tuer' | 'dach' | 'treppe' | 'decke'   (7)
                     app/tools/werkzeugArten.ts:12 — reine UI-Auswahl
                     im Zod-Schema kommt `werkzeug` NICHT vor:
                       node scripts/zaehle.mjs domain/validation.ts 'werkzeug' --wort  ->  0
                     => ein achter Wert aendert KEIN persistiertes Schema.

Vorhandene Geometrie, die wiederverwendet wird statt neu gebaut:
  geometry/polygonFlaeche.ts   polygonFlaecheM2()      Flaeche eines Polygons
  geometry/roomDetection.ts    signierteFlaeche()      Umlaufsinn (Vorzeichen)
  geometry/fangKern.ts         fange()                 Fang — kommt aus Z-02

Was es NICHT gibt: eine Selbstschnitt-Pruefung.
  grep -rl 'selbstschnitt|selfIntersect|schneidetSich' … --include='*.ts'  ->  0 Dateien
```

## Die Entscheidung

**Ein neues Werkzeug `kontur`** — ein achter Wert in der UI-Auswahl. *Nicht `polygon`: dieser Name
gehört zu den sechs stillgelegten Zeichen-Primitiven (Linie, Rechteck, Kreis …), die ausdrücklich
nicht in einen Bauplaner gehören. `kontur` heißt, wofür es da ist: der Umriss eines Bauteils.*

**Die Kontur wird geschlossen durch Klick auf den ersten Punkt (mit Fangtoleranz) oder mit Enter.**
`Escape` verwirft. *Beides ist bereits die Bedienweise von Z-01 — ein Werkzeug endet an genau einer
Stelle.*

**Geprüft wird beim Schließen, nicht beim Klicken.** Drei Bedingungen:
1. **mindestens 3 Punkte** — das verlangt schon das Zod-Schema (`polygon: z.array(punkt2).min(3)`);
2. **kein Selbstschnitt** — neue Funktion `geometry/kontur.ts`;
3. **Fläche größer als null** — sonst liegen alle Punkte auf einer Linie.

**Schlägt eine fehl, bleibt die Kontur offen und die Statusleiste sagt, welche.** *Kein stiller
Abbruch: der häufigste Fehler beim Konturzeichnen ist die Acht, und die sieht man nicht.*

**Diese Scheibe schreibt NICHTS in den Speicher.** Sie liefert die geschlossene Kontur an den
Aufrufer. **Wer sie verwendet, ist Z-06.**

## Kriterien

```yaml
scope:
  dateien:
    - resources/planner/hausplaner/geometry/kontur.ts
    - resources/planner/hausplaner/app/tools/werkzeugArten.ts
    - resources/planner/hausplaner/app/HausplanerApp.tsx
  population_command: "node scripts/zaehle.mjs resources/planner/hausplaner/app/tools/werkzeugArten.ts \"'kontur'\""
  ausschluesse:
    - stelle: "ADD_CEILING und jede Speicher-Wirkung"
      grund: "Eigene Scheibe Z-06. Diese hier liefert die Kontur, sie verwendet sie nicht."
      entschieden_von: planner
    - stelle: "Die sechs stillgelegten Primitive (linie, polylinie, rechteck, kreis, bogen, polygon)"
      grund: "Sie bleiben stillgelegt. `kontur` ist ein Bauteil-Umriss, kein Zeichen-Primitiv."
      entschieden_von: planner
    - stelle: "Loch-Polygone / Aussparungen in der Kontur"
      grund: "Das Schema kennt sie (ceilingOeffnungSchema), aber sie sind eine eigene Entscheidung mit eigenem Bedienweg."
      entschieden_von: planner

kriterien:
  - id: K-01
    typ: presence
    kritikalitaet: P1
    aussage: "Das Werkzeug `kontur` existiert in der UI-Auswahl."
    pruefung:
      befehl: "node scripts/zaehle.mjs resources/planner/hausplaner/app/tools/werkzeugArten.ts \"'kontur'\""
      erwartet: "mindestens 1"
    ausgangswert: "0 (gemessen 01.08. 12:1x)"

  - id: K-02
    typ: presence
    kritikalitaet: P1
    aussage: "Die Selbstschnitt-Pruefung existiert und ist gegen eine ECHTE Acht gepruft."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        Neue Datei geometry/kontur.ts mit `schneidetSichSelbst(punkte)` und
        __tests__/kontur.test.ts mit mindestens diesen Faellen:
          - Rechteck                      -> false
          - L-Form (sechs Punkte)         -> false
          - Acht / Sanduhr                -> true
          - drei Punkte auf einer Linie   -> Flaeche 0, wird abgelehnt
          - zwei Punkte                   -> abgelehnt (Schema verlangt min 3)
        Die Zusagen pruefen die AUSSAGE, nicht die Zahl der Punkte (F-06).
      erwartet: "gruen"

  - id: K-03
    typ: absence
    aussage: "Kein persistiertes Schema wurde angefasst."
    pruefung:
      befehl: "git diff main -- resources/planner/hausplaner/domain/validation.ts | wc -l"
      erwartet: "0"
    ausgangswert: "0"
    gegenbeweis: |
      Die Werkzeugauswahl ist UI-Zustand und kommt im Zod-Schema nicht vor (gemessen: 0).
      Waechst diese Zahl, ist ein persistierter Wert umbenannt worden - und genau das ist
      die stehende Sperre: `type`, `objectType`, `zoneType`, `routeType` bleiben, wie sie sind.

  - id: K-04
    typ: behavioural
    kritikalitaet: P1
    aussage: "Ein Werkzeug endet an genau einer Stelle - auch dieses."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: "npm run test:hausplaner -- --filter=werkzeugEnde"
      erwartet: |
        gruen, 15 Zusagen. Das Konturwerkzeug raeumt seinen Zustand an derselben einen Stelle
        auf wie die anderen - Z-01 hat dafuer sieben Aufraeumstellen zu einer gemacht.
        Wer eine achte anlegt, macht Z-01 rueckgaengig.

  - id: K-05
    typ: behavioural
    aussage: "Die ganze Insel bleibt gruen."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: "npm run test:hausplaner"
      erwartet: "0 fail"

  - id: K-06
    typ: behavioural
    aussage: "Die Mutationsprobe kommt VOR den Tests."
    pruefung:
      typ: verfahren
      schritte: |
        Mindestens 8 Mutationen: Selbstschnitt-Pruefung liefert immer false, `min(3)` auf 2
        gesenkt, Umlaufsinn ignoriert, Schliessen ohne Fangtoleranz, Escape raeumt nicht auf.
        Wie viele kommen durch?

  - id: L-01
    typ: verschoben
    aussage: "Erreichbarkeit im Browser - VERSCHOBEN nach Z-05-N1."
    verschoben_nach: docs/auftraege/generator-auftrag-z05-n1-werkzeug-erreichbar.md
    verschoben_von: planner
    verschoben_am: "01.08.2026"
    grund: |
      Der Generator hat L-01 in 264ab9dc blockiert zurueckgemeldet und den Befund gegen dieses
      Blatt gestellt: scope.dateien nennt drei Dateien, aber L-01 verlangt, im Browser damit zu
      zeichnen. Mit diesen drei Dateien ist das nicht erreichbar - der Registry-Eintrag braucht
      Themen-Bindung, einen Vertrag im Katalog und veraendert die Fix-Zone. Das ist eine eigene
      Scheibe, kein Nachtrag. Der Fehler war meiner (Planner), nicht seiner.
      Z-05 wird auf K-01 bis K-06 abgestimmt. Die Erreichbarkeit ist Z-05-N1.
```

## Rückweg und Entdeckung

**Rückweg:** neues Werkzeug, kein Datenpfad, kein Schema — der Commit lässt sich zurückdrehen.
**Entdeckung:** die Kontur ist sichtbar, während man sie zeichnet. Fällt sie aus, sieht man es sofort.
*Der stille Fall ist die Acht — dafür ist K-02 da.*

## Danach

**Z-06** — die Decke nimmt diese Kontur statt der Bounding-Box. *Das ist die Scheibe, auf die Yama
seit Tagen wartet.*
