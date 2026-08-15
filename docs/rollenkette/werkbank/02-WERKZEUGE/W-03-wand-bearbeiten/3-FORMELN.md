# W-03 · Wand bearbeiten — FORMELN

> **Regel: hier werden nur F-Nummern aus `01-MATHEMATIK/FORMELSAMMLUNG.md` genannt. Keine
> abgeschriebenen Formeln.**

## Die Registerzeile nannte DREI Formeln — keine davon trägt

**`REGISTER.md` führte W-03 unter `F-003, F-004, F-030`. Am Code erhoben, im
Eigenschaften-Panel:**

```text
F-003  Lotfusspunkt auf eine Strecke   lotAufWand im Panel:   0 Treffer
F-004  Schnittpunkt zweier Geraden     geradenGeometrie:      0 Treffer
F-030  Wand aus Achse extrudieren      wandBaender im Panel:  0 Treffer
```

**Gebaut ist stattdessen eine andere:**

| F-Nr | Wofür | Fundstelle |
|---|---|---|
| **F-001** Abstand zweier Punkte | die aktuelle Wandlänge messen, bevor sie gesetzt wird | `EigenschaftenPanel.tsx:117` (`setzeWandLaenge`) und `:339` (Anzeige im Feld) |

> ***F-004 ist gebaut — aber nicht von W-03 aufgerufen.*** *Seit A-32 liegt sie rein in
> `geometry/geradenGeometrie.ts`; das Panel importiert sie **nicht** (0 Treffer).* **Dieser
> Unterschied gehört benannt:** *wer „F-004 ✓" in der Registerzeile liest, liest es als „W-03
> benutzt sie" — und sucht dann im Panel nach einer Schnittpunktrechnung, die dort nicht steht.*

**Berichtigt am 14.08. mit W-03/1** auf `F-001 ✓, ~~F-003~~ ⓝ, ~~F-004~~ ⓝ, ~~F-030~~ ⓝ`.

## Wo F-004 einmal hingehören wird — und heute nicht steht

**Drei der fünf fehlenden Operationen brauchen sie:** `trimmen`, `verlaengern`, `versatz`.

> *Wenn sie gebaut sind, wandert F-004 mit Recht in diese Zeile.* **Heute wäre sie dort eine
> Zusage, die der Code nicht einlöst.**

## Die übrige Mathematik des Panels

```text
Math.round  16x    ganzzahlige mm im Modell
Math.max    13x    Untergrenzen (Feldwerte klemmen)
Math.hypot   3x    Laengen — davon 2 fuer die Wand (F-001), 1 fuer die Treppe
Math.min     1x
```

> *Das Panel bedient **mehr als Wände** — Öffnungen, Dächer, Treppen, Fenster.* **Die Treppen- und
> Fensterrechnungen gehören W-09 bzw. W-04 und sind hier nicht gezählt**, außer wo sie einen der
> obigen Aufrufe erklären.

## Normative Größen

**Keine** — für die Wandbearbeitung. *Was das Panel an Treppen- und Fensterwerten rechnet, trägt
seine Normen in den eigenen Blättern.*
