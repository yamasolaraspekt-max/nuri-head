# Baubericht W-27 — Dachkantentypen. Die Lücke ist der Kantentyp, und die Sache ist da

```yaml
auftrag: "W-27"
rolle: "generator"
blatt: docs/auftraege/aktiv/W-27-dachkantentypen-entwerfen.md
basis_sha: a6101569
gebaut_am: "12.08.2026"
ziel: "ENTWORFEN — Vorgabe aus dem Prototyp, nicht Ablesung des Bestands"
zustand: CODE_FERTIG
ballbesitz: evaluator
```

> **Die Auflage war die Leitlinie dieses Baus, nicht seine Nebenbedingung.** *Kein „gibt es nicht"
> ohne die Zeile daneben, die zeigt, **was da ist**.*

## W-27-1 · Acht Fundstellen, jede einzeln geprüft

```text
docs/planner/pv-belegung-referenz/DachplanerProPage.tsx    3.786 Zeilen
  :85  EdgeTopologyType    :86  TopologyCornerType   :87  TopologyJoinType
  :128 EdgeTopologyConfig  :135 TopologyCornerInfo
  :155 buildTopologyPolygon  :182 getDefaultEdgeTopologyConfigs  :193 analyzeTopology
```

**Alle acht gegen die Datei gelesen, keine übernommen.**

## W-27-2 · Die Regel vollständig — mit zwei Stellen, die das Blatt nicht hatte

*`2-FUNKTION.md` trägt die Entscheidungsregel aus `analyzeTopology` (`:193-230`), einschließlich
`neutral`. **Zwei Präzisierungen kommen aus meiner Lesung der Quelle:***

> ### (1) `prevIsTraufe` heißt nicht „Typ ist TRAUFE"
>
> ```ts
> // :212-213
> const prevIsTraufe = prevEdge?.type === 'TRAUFE' || prevEdge?.type === 'WALM' || prevEdge?.type === 'TEILWALM';
> ```
>
> **Drei Kantentypen zählen als traufseitig.** *Wer die Regel als „beide Kanten sind TRAUFE" liest,
> baut Walm- und Teilwalmdächer falsch — **und das sind genau die Dächer, wegen derer es Grate
> gibt**.*

> ### (2) Der Umlaufsinn ist der Teil, an dem eine eigene Implementierung scheitert
>
> ```ts
> // :205-207
> const cross = n1x * n2y - n1y * n2x;
> const isInnerReflex = isCCW ? cross > 0 : cross < 0;
> const angleDeg = isInnerReflex ? 360 - baseAngle : baseAngle;
> ```
>
> **Der Winkel allein sagt nicht, ob eine Ecke einspringt.** *Wer `isCCW` wegläßt, klassifiziert bei
> umgekehrt gezeichnetem Polygon alle Ecken falsch herum — **und zwar leise**, weil die Zahl der
> Ecken stimmt.* **Als `K-4` in `6-PRUEFUNG.md` festgehalten.**

*Und `neutral` steht als **gültiges Ergebnis** im Blatt, nicht als Restfall: bei einem Satteldach
sind es die beiden Giebelecken.*

## W-27-3 · Die Auflage — je Begriff die Zeile, die zeigt WAS existiert

**Was fehlt, mit den Befehlen gemessen:**

```text
in resources/planner/**/*.ts(x):
  TopologyJoinType 0 · cornerType 0 · joinType 0 · analyzeTopology 0 · isInnerReflex 0
```

**Was DA IST — und hier steht die Zeile, nicht die Zahl:**

| Sache | Fundstelle | Was genau |
|---|---|---|
| **Ortganglänge** | `geometry/dachformVorlagen.ts:291` | `export function ortgangFlaechenlaengeM(…)` — **mit Testzusage** (`dachformVorlagen.test.ts:151-154`, `= 10.6`) |
| **Ortgangausbildung** | `dachformVorlagen.ts:127` · `:1386` · `:1410` | Feld mit Klartextwerten je Dachform |
| **Grat/Kehle am Sparren** | `geometry/schifterListe.ts:58` | `klassifiziereSchifter` → `'kehle' \| 'grat' \| 'voll' \| 'beidseitig'`, getestet (`schifterListe.test.ts:24-27`) |

> ### Ein Fund, der über die Auflage hinausgeht
>
> **Die Auflage nannte den Ortgang. Beim Nachmessen von `grat` und `kehle` fand ich mehr: die Insel
> KLASSIFIZIERT beide bereits** — *nur an einer anderen Sache.*
>
> ```text
> VORHANDEN  klassifiziereSchifter(vStart, vEnd, vMax)
>            fragt: reicht dieser SPARREN bis Traufe und First?   -> Ableitung aus dem SPARREN
> FEHLT      joinType aus Eckwinkel + Kantentypen
>            fragt: was entsteht an DIESER ECKE des Grundrisses?  -> Ableitung aus der GEOMETRIE
> ```
>
> **Beide heißen „Grat" und „Kehle" und meinen dasselbe Bauteil.** *Damit ist auch „die
> Klassifikation fehlt" zu weit formuliert — es fehlt die **Ecken**-Klassifikation.* **Genau der
> Fehlertyp, gegen den die Auflage geschrieben wurde, eine Ebene tiefer.**

*Zur Zahl `grat` 17 / `kehle` 33 aus dem Blatt: ich messe in `resources/planner/**/*.ts`
**case-insensitiv 195 bzw. 157** — die Mengen sind verschieden (Blatt: nur `resources/planner`,
kleingeschrieben; meine: alle `.ts` inklusive Tests, beide Schreibweisen). **Beide Zahlen sind für
ihr Muster richtig; keine widerlegt die andere.***

## W-27-4 · Die Abweichung zur Registerzeile — benannt, Namensliste unangetastet

```text
REGISTER.md:94   "First·Grat·Kehle·Traufe·Ortgang"   — EINE Liste
Prototyp         KANTEN: TRAUFE·GIEBEL·PULT_WAND·WALM·TEILWALM
                 ECKEN:  grat·kehle·ortgang·neutral
```

**Die Trennung wird im Blatt übernommen und die Abweichung benannt.** *In der Registerzeile habe ich
**nur den Reifegrad** gesetzt — die Namensliste bleibt unverändert, wie `W-27-4` es verlangt: sie
wird **beim Bau** nachgezogen, nicht jetzt.*

## W-27-5 · Der Anschluss an W-07 — benannt, nicht gebaut

*W-07 lehnt heute nicht-rechteckige Konturen ab (1 % Toleranz); W-27 würde die Ecken
klassifizieren.* **Was das für W-07s V1-Grenze bedeutet, ist eine Planner-Entscheidung NACH diesem
Auftrag** — im Blatt steht der Zusammenhang, keine Änderung.

## W-27-6 · `must_preserve`

| | Ergebnis |
|---|---|
| `resources/**` · `app/**` | **0 Dateien** |
| **der Prototyp** | **unverändert, 0 Dateien** — nur gelesen |
| **W-07s Blätter** | **unberührt, 0 Dateien** |
| Register | **genau eine Werkzeugzeile**, nur der Reifegrad |
| Abschlusszähler `BESCHRIEBEN` | **13 → 13** — *hier soll er NICHT steigen: Ziel ist `ENTWORFEN`* |
| `ENTWORFEN`-Zeilen | **1 → 2** (nach W-15) |

## Platzhalter — zwei Treffer, keiner ist einer

```text
Rot vorher: 27 (frische Vorlagenkopie)   ·   jetzt: 2
  5-CODE:59 · :60   ReadonlyArray<{ x … }>   TypeScript-Generics
```

*Dritter Bau in Folge mit demselben Befund — **die Platzhalterzählung trifft spitze Klammern**.*

## W-27-7 · §3, zweimal gemessen

```text
vor der ersten Aenderung   Tafelzeile 0 · Zustandsfeld 0
vor der Registerzeile      Tafelzeile 1 · Zustandsfeld 1   (beide W-27)
REGISTER.md dabei          unverändert im Arbeitsbaum — kein fremder Bau darin
```

*Die zweite Messung verlangt das Kriterium ausdrücklich, weil `REGISTER.md` im Scope mehrerer
W-Blätter liegt und am 12.08. bei W-23 im Zugriff war.*

## Berührte Dateien

```text
docs/rollenkette/werkbank/02-WERKZEUGE/W-27-dachkantentypen/   NEU, aus _VORLAGE angelegt
  1-ZWECK.md · 2-FUNKTION.md · 3-FORMELN.md · 4-BEDIENUNG.md
  5-CODE/LIESMICH.md · 6-PRUEFUNG.md · 7-GRENZEN.md
docs/rollenkette/werkbank/02-WERKZEUGE/REGISTER.md             Reifegrad LEER -> ENTWORFEN
docs/BERICHT-W-27-dachkantentypen.md                           dieser Bericht
docs/STATUS.md                                                 Zustand an beiden Orten
```
