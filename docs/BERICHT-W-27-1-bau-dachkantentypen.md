# Baubericht W-27/1 — Dachkantentypen in die Insel gebaut

```yaml
auftrag: "W-27/1"
rolle: "generator"
blatt: docs/auftraege/aktiv/W-27-1-bau-dachkantentypen.md
art: "BAU — der ERSTE Auftrag der Werkbank, der Produktivcode erzeugt"
in_arbeit_commit: "d0d7ec44"
gebaut_am: "12.08.2026"
zustand: CODE_FERTIG
ballbesitz: evaluator
```

> **Der erste gebaute Werkzeugcode der Werkbank.** *Vorher trugen 0 von 43 Werkzeugen `GEBAUT`.*

## Was gebaut wurde

```text
NEU   resources/planner/hausplaner/geometry/dachTopologie.ts        183 Zeilen, 8 Exporte
NEU   resources/planner/hausplaner/__tests__/dachTopologie.test.ts   11 Tests
NACHGEZOGEN  W-27/5-CODE/LIESMICH.md · W-27/6-PRUEFUNG.md
             REGISTER.md Zeile 94:  ENTWORFEN -> GEBAUT
```

**Keine bestehende Datei geändert.** *Der Prototyp bleibt unberührt, `schifterListe.ts` und
`dachGeometrie.ts` ebenso — der Bau ist additiv.*

## W-27/1-1 · Typen und Strukturen — zwei Abweichungen, beide mit Grund

**Was der Vorgabe entspricht, zeichengenau gegen den Prototyp geprüft:**

```text
:85  EdgeTopologyType   TRAUFE · GIEBEL · PULT_WAND · WALM · TEILWALM     unveraendert
:86  TopologyCornerType innen · aussen                                    unveraendert
:87  TopologyJoinType   grat · kehle · ortgang · neutral                  unveraendert
:128 EdgeTopologyConfig { id, type, pitch, label }                        unveraendert
:135 TopologyCornerInfo { index, point, angleDeg, cornerType, joinType }  unveraendert
```

**Abweichung 1 — die Rückgabe ist `TopologyAnalysis`, nicht `TopologyCornerInfo[]`.**

```text
Auftragsblatt Abschnitt 1   analyzeTopology(…): TopologyCornerInfo[]
Quelle :193                 analyzeTopology(…): TopologyAnalysis
Quelle :143                 interface TopologyAnalysis { points, edgeConfigs, corners,
                              innenEcken, aussenEcken, grate, kehlen, ortgaenge }
W-27s Vorgabe, 5-CODE       ":193 function analyzeTopology(points, edgeConfigs)"  OHNE Rueckgabe
```

> **Der `TopologyCornerInfo[]` stammt aus dem Auftragsblatt und nicht aus der Vorgabe, auf die es
> sich beruft.** *Gemeldet **vor** dem Bau, im `IN_ARBEIT`-Commit `d0d7ec44`, damit der Evaluator
> gegen die richtige Erwartung prüft.* **Gebaut nach der Quelle — und die fünf Zählungen sind genau
> die Zahlen, deren Fehlen W-27s eigene Ablesung als Lücke gemessen hat (`joinType`/`cornerType`
> je 0).**

**Abweichung 2 — `TopologyPoint` wird exportiert.** *Die Vorgabe führt ihn nur als Fundstelle
`:123`; ohne Export wäre die Eingabe der öffentlichen Funktion nicht benennbar.*

**Eine dritte Änderung, die keine Abweichung der Schnittstelle ist:** *zwei Hilfsfunktionen sind
benannt statt eingebettet* — `istGegenUhrzeigersinn` (Schritt 0) und `istTraufeImWeiterenSinn`.
**Beide sind nicht exportiert.** *Der Grund steht im Code: Schritt 0 ist der Schritt, dessen Fehlen
**leise** falsch klassifiziert; ein eigener Name macht ihn sichtbar.*

## W-27/1-2 · Alle vier Schritte, alle vier Ausgänge — die Zeilen

```text
dachTopologie.ts:127   const isCCW = istGegenUhrzeigersinn(points);      Schritt 0
              :133-149 Winkel: hypot, dot geklemmt, acos, cross,          Schritt 1
                       isInnerReflex, angleDeg = 360 - baseAngle
              :152     cornerType = angleDeg > 180 ? 'innen' : 'aussen'   Schritt 2
              :160-168 joinType: 'neutral' als Default, dann              Schritt 3
                       Grat/Kehle, dann Ortgang
```

**Die vier Ausgänge sind vollständig:** *`'neutral'` steht als **Vorbelegung** in `:160`, nicht als
Restfall — deshalb kann keine Ecke `undefined` tragen.* **Der Test `jede Ecke trägt IMMER einen der
vier Ausgänge` prüft genau das über drei verschiedene Kantenfolgen.**

## W-27/1-3 · Die Namensgrenze, im Code sichtbar

**`dachTopologie.ts:4-17`, der Dateikopf:**

> *„Die Wörter `'kehle'` und `'grat'` gibt es in dieser Insel **zweimal**, und sie beantworten
> **verschiedene Fragen**: `geometry/schifterListe.ts` → `klassifiziereSchifter(…)` fragt, ob ein
> **STAB** angeschnitten ist … **Diese Datei** fragt, was an einer **ECKE des Grundrisses**
> entsteht … **Gleiche Wörter, andere Sache.**"*

**Damit findet ein Leser die Grenze dort, wo er sie braucht** — *in der Datei, die er gerade offen
hat, und nicht nur im Werkbankblatt.*

## W-27/1-4 · Die drei Fangproben — gefahren, alle drei gefallen

```text
Anker md5 vor den Proben   bfc684226f02161448ef02d20b7629f3
Grundlinie                 1709 Tests · 1709 pass · 0 fail

M1  isCCW = true (Schritt 0 weg)     1707 pass · 2 FAIL
      ✖ K-4 TRAGEND: der Umlaufsinn — dasselbe Polygon in beiden Richtungen
      ✖ K-4: die L-Form hat GENAU EINE einspringende Ecke
M2  nur 'TRAUFE' (WALM/TEILWALM weg) 1708 pass · 1 FAIL
      ✖ WALM und TEILWALM zaehlen als Traufe im weiteren Sinn
M3  kein 'neutral'-Default           1707 pass · 2 FAIL
      ✖ der VIERTE Ausgang: … ist neutral — nicht undefined
      ✖ jede Ecke traegt IMMER einen der vier Ausgaenge — nie undefined

md5 nach jedem Ruecksetzen  bfc684226f02161448ef02d20b7629f3   IDENTISCH
```

> **Keine der drei blieb grün.** *Das ist der Unterschied zu W-34-1 und W-39-5, wo je eine Fangprobe
> nichts fing — dort war es eine **Aussage über** einen Wächter, hier eine **Messung an** ihm.*

## W-27/1-5 · Suite und Typprüfung am Bau-Stand

```text
vorher   1698 Tests · 1698 pass · 0 fail
nachher  1709 Tests · 1709 pass · 0 fail      (+11 neue)
npm run tsc:hausplaner                        gruen
```

## W-27/1-6 · Übergangsprüfung — Vorgabe gegen Ablesung des gebauten Codes

> *Aus der Registerlegende: „Ein Entwurf, der gebaut wurde und danach nicht nachgemessen wird, ist
> eine unbelegte Behauptung über den eigenen Code."*

**Der gebaute Code, abgelesen wie ein fremder:**

| Vorgabe (W-27) | Gebaut | Abweichung |
|---|---|---|
| 3 Typlisten | `:40` `:43` `:51` | — |
| `EdgeTopologyConfig` `:128` | `:54` | — |
| `TopologyCornerInfo` `:135` | `:62` | — |
| *(nur als Fundstelle `:123`)* | **`:34` `TopologyPoint`, exportiert** | **Abweichung 2** |
| *(kein Rückgabetyp genannt)* | **`:77` `TopologyAnalysis`** | **Abweichung 1** |
| `analyzeTopology(points, edgeConfigs)` | `:123`, vier Schritte benannt | — |

**Beide Abweichungen stehen oben mit Grund.** *Die Vorgabe ist sonst zeichengenau umgesetzt; keine
stillschweigende Umbenennung.*

## W-27/1-7 · W-27s Blätter nachgezogen

```text
5-CODE/LIESMICH.md   „Wo der Code leben WIRD" -> „Wo der Code lebt — seit W-27/1 GEBAUT",
                     mit den acht Exporten und den beiden Abweichungen
6-PRUEFUNG.md        die Fangprobentabelle traegt jetzt eine Spalte „Gefahren?" und die
                     drei gefahrenen Proben mit Zaehlerstand
REGISTER.md :94      ENTWORFEN -> GEBAUT
```

## W-27/1-8 · Nichts Fremdes berührt

```text
git show <bau-sha> --name-only
  resources/planner/hausplaner/geometry/dachTopologie.ts        NEU
  resources/planner/hausplaner/__tests__/dachTopologie.test.ts  NEU
  docs/rollenkette/werkbank/02-WERKZEUGE/REGISTER.md
  docs/rollenkette/werkbank/02-WERKZEUGE/W-27-dachkantentypen/5-CODE/LIESMICH.md
  docs/rollenkette/werkbank/02-WERKZEUGE/W-27-dachkantentypen/6-PRUEFUNG.md
  docs/BERICHT-W-27-1-bau-dachkantentypen.md                    NEU
  docs/STATUS.md                                                nur W-27/1s eigener Zustand
```

**Der Prototyp ist unberührt**, *`schifterListe.ts` ebenso, und keine andere Dachdatei.*

## must_preserve und Rückweg

| | Ergebnis |
|---|---|
| bestehende Dateien in `geometry/` | **0 geändert** |
| Prototyp `docs/planner/pv-belegung-referenz/` | **0 geändert** |
| Datenbank · Produktdaten · Backend | **0** — der Bau bleibt in `geometry/` |
| Rückweg | zwei neue Dateien plus drei Doku-Änderungen; `git revert` genügt |
