# W-10 · Decke und Boden — FORMELN

> **Am Code erhoben, nicht aus der Registerzeile übernommen.** *Die Zeile sagte `F-011, F-030`
> (`REGISTER.md:47`). Eine trägt, eine trägt zur Hälfte, und **eine dritte fehlt**.*

## Die Bilanz gegen die Registerzeile

| Nummer | Registerzeile | gemessen |
|---|---|---|
| **F-011** Fläche eines Polygons | genannt | **trägt** — `polygonFlaecheM2` ist die Gaußsche Flächenformel, Wort für Wort |
| **F-030** Wand aus Achse extrudieren | genannt | **trägt zur Hälfte** — die 2D-Hälfte exakt, die Extrusion gar nicht |
| **F-001** Abstand zweier Punkte | **fehlt** | **`Math.hypot` auf `applyCommand.ts:127`** — die Lauflänge, ohne die es keine Normale gibt |

## F-011 trägt — und zwar wörtlich

**Sammlung:** `A = ½·|Σᵢ (xᵢ·yᵢ₊₁ − xᵢ₊₁·yᵢ)|`

**Code** (`geometry/polygonFlaeche.ts:44` und `:46`, selbst geöffnet):

```text
:44   summe += a.x * b.y - b.x * a.y;
:46   const flaeche = Math.abs(summe) / 2;
```

> ***Dieselbe Formel, dieselbe Betragsbildung, dieselbe Halbierung.*** *Der Dateikopf nennt sie beim
> Namen* (`:10`, *„Gaußsche Flächenformel (Shoelace)")*.

**Und `deckenNettoFlaecheM2` setzt eine Subtraktion darauf** (`deckenMesh.ts:18-25`): *Brutto minus
Summe der Löcher,* **`Math.max(0, …)` an drei Stellen** — *nie negativ.*

> **Diese Subtraktion steht NICHT in F-011.** *Die Sammlung beschreibt die Fläche eines Polygons;
> „Fläche minus Lochpolygone" ist eine eigene Größe.* **Als Lücke gemeldet, keine Nummer erfunden**
> — *die Lehre aus W-21.*

### Der Grenzfall von F-011 zeigt auf W-18

*Die Sammlung schreibt: „**Selbstschneidendes Polygon liefert eine falsche, aber plausible Zahl** —
keine Fehlermeldung. Deshalb vorher F-013 laufen lassen."*

**Auf dem Kontur-Weg ist das erfüllt:** `HausplanerApp.tsx:831` ruft `pruefeKontur` — *das ist F-013
aus `geometry/kontur.ts` (W-18).* **Auf dem Umriss-Weg** (`gebaeudeUmriss()`, `:1031`) *habe ich es
nicht gemessen und behaupte es deshalb nicht.* **Offene Stelle, benannt statt geraten.**

## F-030 trägt zur Hälfte — und die Hälfte ist exakt

**Sammlung:**

```text
Richtung  r = (B−A)/|B−A|
Normale   n = (−r_y, r_x)          ← 90° gedreht
Vier Grundpunkte:  A ± (d/2)·n ,  B ± (d/2)·n
Extrusion in z:    von z₀ bis z₀+h
```

**Code** (`applyCommand.ts:126-136`):

```text
:126  dx, dy            = B − A
:127  len = Math.hypot  = |B − A|
:128  nx = -dy/len, ny = dx/len        ->  n = (−r_y, r_x)      DECKUNGSGLEICH
:129  h  = tp.laufbreite / 2           ->  d/2                  DECKUNGSGLEICH
:131  start ± n·h  und  end ± n·h      ->  A ± (d/2)·n , B ± …  DECKUNGSGLEICH
```

> ***Die ersten drei Schritte von F-030 stehen hier Zeile für Zeile*** — *und der vierte,* **die
> Extrusion in z, kommt nicht vor.** *Es entsteht ein flaches Loch-Polygon und kein Quader.*
>
> **Deshalb ist `F-030` in der Registerzeile nicht falsch, sondern zu groß**: *die Zeile verspricht
> einen Körper, gebaut ist die Grundfläche.* **Und der Grund ist sachlich** — *ein Treppenauge ist
> ein Loch in einer Ebene, keine Wand.*

## F-001 fehlt in der Registerzeile

**`Math.hypot(dx, dy)`** (`:127`) *ist F-001:* `d = √((x₂−x₁)² + (y₂−y₁)²)`.

> ***Ohne sie gibt es keine Normale*** — *`nx = -dy/len` teilt genau durch dieses `len`.* **Sie ist
> keine Nebenrechnung, sondern die Voraussetzung der zweiten.**

### Und ihr Grenzfall ist hier ANDERS gelöst als in der Sammlung

*Die Sammlung sagt zu F-001: „`d < ε` (0,5 mm) → beide Punkte gelten als **derselbe**. Eine Wand mit
`d < ε` darf nicht angelegt werden — sie erzeugt später eine Division durch null."*

**Der Code sagt `|| 1`** (`:127`): *bei `len === 0` wird 1 eingesetzt, und es entsteht ein
entartetes, aber endliches Loch.*

> ***Das ist keine Abweichung, die ich als Fehler melde*** — *eine Treppe ohne Länge ist etwas
> anderes als eine Wand ohne Länge, und eine Absage mitten im Reducer wäre eine Produktentscheidung.*
> **Festgehalten ist, DASS die zwei Wege verschieden sind**, *damit die nächste Rolle den Unterschied
> nicht für ein Versehen hält.*

## Zwei Additionen ohne Nummer — und das ist richtig so

```text
deckenMesh.ts:11   elevation + defaultWallHeight
             :34   Math.round(elevation + defaultWallHeight + deckeDickeMm)
```

> **Für „zwei Höhen addieren" braucht es keine Formelnummer.** *Genannt, damit niemand sie sucht.*

## Ein Befund am Rande, gemessen: der Name sagt m², die Rechnung sagt nichts

**`polygonFlaecheM2` heißt `…M2` und rechnet KEINE Einheit um** — *die Shoelace-Formel ist quadratisch
und liefert die Einheit, die man hineingibt.*

```text
geometry/polygonFlaeche.ts:12   Dateikopf: „in Metern"
renderers/three-d/deckenMesh.ts:14   „polygonFlaecheM2 rechnet KEINE Einheit um
                                      (Input in mm ⇒ Ergebnis mm²)"  -> /1e6
__tests__/decke.test.ts:128     „// polygonFlaecheM2 rechnet in mm²"  -> /1e6
geometry/grundriss.ts:111       grundrissFlaecheM2 -> reicht durch, ohne Umrechnung
```

> ***Beide Verbraucher rechnen richtig, und beide mussten es dazuschreiben.*** *Das Ergebnis stimmt
> — `decke.test.ts:122` beweist 68 m² aus mm-Koordinaten.* **Der Name ist die Falle, nicht die
> Rechnung**, *und `deckenMesh.ts:14` ist die Stelle, an der jemand sie schon einmal gestellt bekommen
> hat.* **Gemeldet, nicht behoben — eine Ablesung ändert ihre Quelle nicht.**
