# W-21 · Sparren und Lattung — FORMELN

**Nur Nummern.** Die Formeln stehen in `../../01-MATHEMATIK/FORMELSAMMLUNG.md`.

## Das Register nennt F-001 und F-030 — gemessen steht keine von beiden im Code

| F-Nr | laut Register | gemessen |
|---|---|---|
| **F-001** Abstand zweier Punkte | ja | **NEIN** — kein `Math.hypot`, kein `Math.sqrt` in **keinem** der fünf Module |
| **F-030** Wand aus Achse extrudieren | ja | **NEIN** — keine Extrusion; die Stäbe kommen fertig aus der 3D-Engine |

**Das ist stimmig, sobald man die Natur der Module kennt:** drei von fünf **aggregieren** aus einer
bereits erzeugten Liste — *sie brauchen keine Geometrieformel, weil sie keine Geometrie erzeugen.*

## Die gemeldete Lücke: `bodenschneelast()` und `formbeiwertSchnee()` haben keine F-Nummer

Beide rechnen — `sparrenBerechnung.ts:33` und `:45` — und **die Sammlung kennt sie nicht.**

**Das ist kein Mangel der Sammlung.** Es sind **normative Größen**, keine Geometrieformeln, und ihre
Quelle steht im Dateikopf:

```text
Schneelast       DIN EN 1991-1-3 + Nationaler Anhang
                 (charakt. Bodenschneelast sk je Zone/Hoehe, Formbeiwert my1 nach Dachneigung)
Holzbemessung    DIN EN 1995-1-1 (Eurocode 5): Biegenachweis und Durchbiegung
```

> **Eine erfundene F-Nummer wäre schlimmer als eine gemeldete Lücke.** *Die Sammlung ist ein
> Geometrie-Verzeichnis; eine Norm gehört nicht hinein, nur weil sie eine Zahl liefert.*

## `auswechslung.ts` (W-21/2, 13.08.) — KEINE F-Nummer, und das ist gemessen

**Am Code erhoben, nicht geraten.** Die gesamte Mathematik des Moduls — **zwölf Aufrufe auf zehn
Zeilen**, mehr steht nicht darin:

```text
Math.max     :71 :72 :74 :95 :96 :109 :110 :159   Untergrenze erzwingen
             :145                                 groesster Kandidat einer Liste
Math.min     :74                                  Obergrenze 2000 Sparren
             :146                                 kleinster Kandidat einer Liste
Math.floor   :74                                  Anzahl Sparrenfelder
```

**Kein `Math.hypot`, kein `Math.sqrt`, kein `Math.cos`, kein `Math.atan2`** — *derselbe Befund wie
bei den fünf anderen Modulen.* Das Modul rechnet drei Dinge: **Klemmungen** (Werte in gültige Grenzen
zwingen), **Vergleiche von Intervallen** (welche Rasterposition liegt im u-Bereich der Öffnung) und
**die Auswahl des nächsten Nachbarn** (`:145/:146` — der größte Sparren links davon, der kleinste
rechts davon). **Dafür kennt die Sammlung keine Nummer, und sie braucht auch keine.**

**Die zwei, die man prüfen muss, weil sie beinahe passen — beide gemessen und beide NEIN:**

| Kandidat | warum es naheliegt | warum es nicht zutrifft |
|---|---|---|
| **F-040** Rasterfang | beide heißen „Raster" | F-040 ist `x' = runde(x/g)·g` — ein Punkt wird auf ein Gitter **gezogen**. `sparrenPositionenU()` **erzeugt** eine Gleichverteilung `rw/2 + i·((b−rw)/n)`; nichts wird gerundet, nichts gefangen |
| **F-032** Transformation eines Punktes | `xRel · breite` ist eine Skalierung | F-032 ist die homogene **4×4-Matrix** mit Reihenfolge-Regel. Eine Multiplikation mit einem Skalar ist nicht ihr Gegenstand |

> **Eine erfundene F-Nummer wäre schlimmer als eine gemeldete Lücke** — derselbe Satz wie oben, und
> hier ist er zum zweiten Mal angewandt. *Alle 26 F-Nummern der Sammlung durchgesehen, nicht nur die
> nächstliegenden.*

**Und keine N-Nummer:** `auswechslung.ts` rechnet **keine normative Größe**. *Es entscheidet
Geometrie und übergibt die Statik ausdrücklich — `pruefpflichtig`, siehe `7-GRENZEN`.*

## Die N-Reihe von W-21: es sind DREI, nicht zwei

**Gemessen an den Belegstellen der Sammlung — alle drei zeigen auf ein W-21-Modul:**

| N-Nr | Ampel | Belegstelle laut Sammlung | im Modul |
|---|---|---|---|
| **N-001** Charakteristische Bodenschneelast sₖ | 🟢 | `geometry/sparrenBerechnung.ts:33` | `bodenschneelast()` |
| **N-002** Formbeiwert μ₁ | 🟢 | `geometry/sparrenBerechnung.ts:45` | `formbeiwertSchnee()` |
| **N-003** Sparren-Vorbemessung | **🟡 FACH-GATE** | `geometry/sparrenBerechnung.ts:86` | `berechneSparren()` |

> **Der Auftrag W-21-2-5 nannte N-001 und N-002. Gemessen sind es drei.** *Beide Zahlen stehen hier,
> die Entscheidung liegt bei der Abnahme.* **Der plan-prüfer hat es beim DoR ebenso gemessen und
> ausdrücklich nicht als Blocker gewertet, weil die Methode des Kriteriums — „am Code erhoben" —
> zwangsläufig auf `sparrenBerechnung.ts` und damit auf N-003 führt.**

**Warum das mehr ist als eine Zahl:** **N-003 ist genau die Nummer mit Yamas FACH-GATE** (DAUERGELB,
Geltungsbereich von ihm am 12.08. festgelegt) **und der A-14-Ausgabeauflage** *„jede ausgegebene
Bemessungszahl trägt ihren Vorbehalt mit"*. *Wer W-21 mit N-001 und N-002 führt, lässt ausgerechnet
die Norm weg, die ein offenes Gate und eine Auflage trägt — am Werkzeug, dessen Modul sie rechnet.*

**Die Auflage ist im Code angekommen — und die Belegstelle der Sammlung dadurch überholt:**

```text
sparrenBerechnung.ts:100   N003_VORBEHALT = 'Vorbemessung, ersetzt keine prüffähige Statik'
sparrenBerechnung.ts:105   berechneSparren()      <- steht NICHT mehr auf :86
N-003 (Sparren-Vorbemessung)      Belegstelle: geometry/sparrenBerechnung.ts:86
```

*Hier nur gemeldet: **die Formelsammlung gehört nicht zu W-21** und ihre Berichtigung ist nicht der
Auftrag von W-21/2. Wer sie anfasst, prüft `:86` gegen `:105`.*

## Was sonst gerechnet wird

`sparrenBerechnung.ts:90` benutzt `Math.cos` für die **senkrechte Lastkomponente** — der Dateikopf
nennt genau das als Annahme der Vorbemessung. *Kein Winkel wird gemessen; der Winkel ist Eingabe.*
