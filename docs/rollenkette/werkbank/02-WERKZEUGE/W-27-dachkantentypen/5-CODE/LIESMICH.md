# W-27 · CODE

## NOCH NICHT GEBAUT — die Vorlage steht im Prototyp, nicht in der Insel

```text
QUELLE   docs/planner/pv-belegung-referenz/DachplanerProPage.tsx      3.786 Zeilen

  :85   type EdgeTopologyType   = 'TRAUFE'|'GIEBEL'|'PULT_WAND'|'WALM'|'TEILWALM'
  :86   type TopologyCornerType = 'innen'|'aussen'
  :87   type TopologyJoinType   = 'grat'|'kehle'|'ortgang'|'neutral'
  :128  interface EdgeTopologyConfig { id, type, pitch, label }
  :135  interface TopologyCornerInfo { index, point, angleDeg, cornerType, joinType }
  :155  function buildTopologyPolygon(build)
  :182  function getDefaultEdgeTopologyConfigs(build, pointCount)
  :193  function analyzeTopology(points, edgeConfigs)
```

**Alle acht Fundstellen sind einzeln gegen die Datei geprüft, nicht übernommen.**

## Wo der Code lebt — seit W-27/1 GEBAUT

| Schicht | Datei im Repo | Zweck | Zustand |
|---|---|---|---|
| 2 Geometrie | **`resources/planner/hausplaner/geometry/dachTopologie.ts` (183 Z.)** | Eckwinkel, Eckenart, Verbindungsart | **GEBAUT** (W-27/1) |
| Test | **`resources/planner/hausplaner/__tests__/dachTopologie.test.ts`** | elf Tests, drei gefahrene Fangproben | **GEBAUT** |
| 3 Werkzeug | — | keine eigene Werkzeugschicht, die Analyse läuft mit | offen |
| 4/5 | Anzeige der Ecktypen am Modell | — | **fehlt**, nicht Gegenstand |

**Die acht Exporte der gebauten Datei, am Bau-Stand abgelesen:**

```text
:34   interface TopologyPoint        { x, y }
:40   type EdgeTopologyType          TRAUFE · GIEBEL · PULT_WAND · WALM · TEILWALM
:43   type TopologyCornerType        innen · aussen
:51   type TopologyJoinType          grat · kehle · ortgang · neutral
:54   interface EdgeTopologyConfig   { id, type, pitch, label }
:62   interface TopologyCornerInfo   { index, point, angleDeg, cornerType, joinType }
:77   interface TopologyAnalysis     { points, edgeConfigs, corners, + FUENF Zaehlungen }
:123  function analyzeTopology(points, edgeConfigs): TopologyAnalysis
        :127 Schritt 0 Umlaufsinn · :133 Schritt 1 Winkel
        :151 Schritt 2 Eckenart   · :154 Schritt 3 Verbindungsart
```

> **Zwei Abweichungen gegenüber der Vorgabe, beide im Baubericht mit Grund:** *`TopologyPoint`
> wird **exportiert** (die Vorgabe nannte ihn nur als Fundstelle `:123`), und die Rückgabe ist
> **`TopologyAnalysis`** statt `TopologyCornerInfo[]` — so steht es in der Quelle `:193`, und die
> fünf Zählungen sind genau die Zahlen, deren Fehlen diese Ablesung als Lücke gemessen hat.*
>
> **Und die Namensgrenze zu `klassifiziereSchifter` steht jetzt im Dateikopf des gebauten Codes**
> (`dachTopologie.ts:4-17`) — *dort, wo sie ein Leser findet, der `'kehle'` an zwei Orten sieht.*

## Kernstelle — die Ableitung, auf die es ankommt

```ts
// DachplanerProPage.tsx:205-208
const cross = n1x * n2y - n1y * n2x;
const isInnerReflex = isCCW ? cross > 0 : cross < 0;
const angleDeg = isInnerReflex ? 360 - baseAngle : baseAngle;
const cornerType: TopologyCornerType = angleDeg > 180 ? 'innen' : 'aussen';

// :215-217
let joinType: TopologyJoinType = 'neutral';
if (prevIsTraufe && nextIsTraufe) joinType = cornerType === 'innen' ? 'kehle' : 'grat';
else if ((prevIsTraufe && nextEdge?.type === 'GIEBEL') || (nextIsTraufe && prevEdge?.type === 'GIEBEL')) joinType = 'ortgang';
```

*Und die Definition, die man leicht überliest (`:212-213`):* **`prevIsTraufe` ist wahr für `TRAUFE`,
`WALM` **und** `TEILWALM`** — nicht nur für `TRAUFE`.

## Schnittstelle — Vorgabe für den Bau

```ts
// VORGABE. Feldnamen aus der Quelle zitiert; die Aufteilung in ein reines Modul ist Entwurf.
export type Kantentyp   = 'TRAUFE' | 'GIEBEL' | 'PULT_WAND' | 'WALM' | 'TEILWALM';
export type Eckenart    = 'innen' | 'aussen';
export type Verbindung  = 'grat' | 'kehle' | 'ortgang' | 'neutral';

export interface EckenBefund {
  index: number; punkt: { x: number; y: number };
  winkelGrad: number; eckenart: Eckenart; verbindung: Verbindung;
}
export function analysiereKanten(
  punkte: ReadonlyArray<{ x: number; y: number }>,
  kanten: ReadonlyArray<Kantentyp>,
): { ecken: EckenBefund[]; innenEcken: number; aussenEcken: number;
     grate: number; kehlen: number; ortgaenge: number };
```

## Was die INSEL heute hat — mit Trefferzeile, nicht mit Zahl

> **Diese Tabelle ist die Auflage dieses Auftrags in Reinform.** *Ohne sie liest der Nächste „fehlt"
> und baut etwas neu, das es gibt.*

| Sache | Fundstelle | Was genau |
|---|---|---|
| **Ortganglänge** | `geometry/dachformVorlagen.ts:291` | `export function ortgangFlaechenlaengeM(lengthM, …)` — **mit Testzusage**, `dachformVorlagen.test.ts:151-154`: `ortgangFlaechenlaengeM(10, 0.3) = 10.6` |
| **Ortgangausbildung** | `dachformVorlagen.ts:127` (Feld), `:1386`, `:1410` (Werte) | Klartext je Dachform, z. B. „Ortgangabschluss", „Attika-Abdeckung mit Tropfkante" |
| **Grat/Kehle am Sparren** | `geometry/schifterListe.ts:58` | `klassifiziereSchifter(vStart, vEnd, vMax, tol)` → `'kehle' \| 'grat' \| 'voll' \| 'beidseitig'` |

**Und was fehlt, mit denselben Befehlen gemessen:**

```text
TopologyJoinType   0        cornerType        0        joinType          0
analyzeTopology    0        isInnerReflex     0
   — gesucht in resources/planner/**/*.ts(x)
```

> ### Der Unterschied, auf den alles ankommt
>
> ```text
> VORHANDEN  klassifiziereSchifter  ->  fragt: reicht dieser SPARREN bis Traufe und First?
>                                       Ableitung aus dem SPARRENVERLAUF.
> FEHLT      joinType               ->  fragt: was entsteht an DIESER ECKE des Grundrisses?
>                                       Ableitung aus WINKEL + KANTENTYPEN.
> ```
>
> **Beide heißen „Grat" und „Kehle" und meinen dasselbe Bauteil.** *Die eine leitet es aus dem
> Sparren ab, die andere aus der Geometrie.* **Die Insel hat die erste. Was fehlt, ist die zweite —
> und das ist der ganze Auftrag.**

## Abhängigkeiten

| Braucht | Warum | Richtung geprüft? |
|---|---|---|
| **W-07** (Kontur) | ohne Polygon keine Ecken | **ja** — und W-07 lehnt heute genau die Konturen ab, für die W-27 gebaut würde |
| Kantentypen je Kante | Eingabe, keine Ableitung | ja — Festlegung durch Anwender oder Vorbelegung (`:182`) |
| **F-025** | die Fachregel | ja, 🟢 in der Sammlung |
| Straight Skeleton (F-020/F-021) | **NEIN** — anderer Weg, 0 Treffer auf `skelett` in acht Dachmodulen | kein Kreis, kein Bedarf |
