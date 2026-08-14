# W-12 · Ansicht und Kamera — FORMELN

> **Regel: hier werden nur F-Nummern aus `01-MATHEMATIK/FORMELSAMMLUNG.md` genannt. Keine
> abgeschriebenen Formeln.**

## Benutzte Formeln

| F-Nr | Wofür in diesem Werkzeug | Grenzfall betrifft uns? |
|---|---|---|
| **F-032** Transformation eines Punktes | 3D-Geometrie an ihren Platz bringen | **ja** — s. u. |

**Fundstelle am Bau-Stand, gebaute Form:**

```text
renderers/three-d/szene.ts:621   const m = new THREE.Matrix4().makeBasis( … )
                          :627   geometrie.applyMatrix4(m)
```

**Die Sammlung führt F-032 als homogene 4×4-Matrix mit `P' = M · P`.** *Hier wird sie aus
Basisvektoren aufgebaut (`makeBasis`) und auf eine Geometrie angewandt — dieselbe Sache, in der
Schreibweise von `three`.*

**F-032 führt ZWEI Grenzfälle — beide am Bau-Stand nachgesehen, nicht aus dem Auftrag übernommen:**

**Grenzfall 1 — Reihenfolge.** *„Matrixmultiplikation ist **nicht** kommutativ. Erst drehen, dann
verschieben ist etwas anderes als umgekehrt. **Reihenfolge im Code festschreiben.**"*

> **Betrifft uns, und er ist erfüllt:** *`makeBasis` setzt die Achsen **direkt** — es gibt an dieser
> Stelle keine Verkettung, in der man die Reihenfolge vertauschen könnte.*

**Grenzfall 2 — Rundung** *(ergänzt am 14.08.)*: *„F-032 kennt **kein Runden**; wer das Ergebnis auf
ganze mm rundet, verlässt die Formel."*

> ***Betrifft uns NICHT — und der Grund gehört dazu.*** *Der Grenzfall ist an der **Achsenspiegelung**
> nachgerechnet (`editierGeometrie.ts:34`), also an einer Stelle, die ein **Modellergebnis** rundet.*
> **`szene.ts:621/:627` rundet nicht:** *`applyMatrix4` schreibt in die Darstellungsgeometrie und
> nicht ins Dokument.* **Was die Kamera zeigt, wird nicht zurückgelesen** — es entsteht kein Rückweg,
> auf dem eine Drift auffallen könnte.

*Beide Fundstellen stehen als **Anker** und nicht als Zeilennummer der Sammlung: eine Einfügung
verschiebt jede Zahl dahinter lautlos (A-34) — und genau das ist der Grenzfall-Abschnitt heute
selbst gewesen.*

## Was sonst NICHT gerechnet wird

**Kamera und Steuerung rechnen nichts Eigenes.** *`THREE.PerspectiveCamera` und `OrbitControls`
kommen aus `three`; Umkreisen, Zoomen und Schwenken sind deren Sache.* **Ein Nachbau wäre eine
zweite Wahrheit.**

**Das 2D-Raster rechnet in Weltmaßen, nicht in Bildschirmpunkten** (`HausplanerApp.tsx:1262-1263`):

```text
weltBreite = breite / zoom
weltHoehe  = hoehe  / zoom
```

> *Deshalb bleibt die Rasterweite beim Zoomen ein **Maß** und wird nicht enger — sie hätte sonst
> keine Bedeutung mehr, sondern wäre Zierde.* **Dafür kennt die Sammlung keine Nummer, und sie
> braucht auch keine: es ist eine Division.**

## Normative Größen

**Keine.** *Weder Ansicht noch Kamera noch Raster tragen eine Norm.*
