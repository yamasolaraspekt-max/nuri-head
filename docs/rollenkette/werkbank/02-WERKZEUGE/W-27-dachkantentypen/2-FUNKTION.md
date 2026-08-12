# W-27 · Dachkantentypen — FUNKTION

> **Vorgabe aus der Quelle, jede Struktur mit Fundstelle.**
> *`docs/planner/pv-belegung-referenz/DachplanerProPage.tsx`, 3.786 Zeilen.*

## Die drei Typlisten — zitiert, nicht entworfen

```ts
// :85
type EdgeTopologyType   = 'TRAUFE' | 'GIEBEL' | 'PULT_WAND' | 'WALM' | 'TEILWALM';
// :86
type TopologyCornerType = 'innen' | 'aussen';
// :87
type TopologyJoinType   = 'grat' | 'kehle' | 'ortgang' | 'neutral';
```

> **Die Trennung ist der eigentliche Inhalt dieses Blattes:** *`EdgeTopologyType` beschreibt eine
> **KANTE**, `TopologyJoinType` beschreibt, was an einer **ECKE** entsteht.* **First und Grat sind
> keine Kantentypen — sie entstehen zwischen zwei Kanten.** *Siehe die Abweichung zur Registerzeile
> in `7-GRENZEN.md`.*

## Eingabe

| Was | Typ | Pflicht | Quelle |
|---|---|---|---|
| `points` | `TopologyPoint[]` — der Grundriss als Polygon | ja | `:193` |
| `edgeConfigs` | `EdgeTopologyConfig[]` — je Kante ein Typ | ja | `:193`, Struktur `:128` |

```ts
// :128
interface EdgeTopologyConfig  { id, type, pitch, label }
// :135
interface TopologyCornerInfo  { index, point, angleDeg, cornerType, joinType }
```

*Die Kantenkonfiguration entsteht aus `getDefaultEdgeTopologyConfigs(build, pointCount)` (`:182`),
das Polygon aus `buildTopologyPolygon(build)` (`:155`).*

## Verarbeitung — die Entscheidungsregel, VOLLSTÄNDIG

*Aus `analyzeTopology` (`:193-230`) abgelesen. **Alle vier Ausgänge**, einschließlich `neutral`.*

```text
0  UMLAUFSINN      signedArea > 0  ->  isCCW                                    (:194-195)
   Er entscheidet, welche Kreuzproduktrichtung „einspringend" bedeutet.

1  ECKENWINKEL     baseAngle  = acos(n1·n2) in Grad                             (:203-204)
                   isInnerReflex = isCCW ? cross > 0 : cross < 0                (:205-206)
                   angleDeg   = isInnerReflex ? 360 - baseAngle : baseAngle     (:207)

2  ECKENART        cornerType = angleDeg > 180 ? 'innen' : 'aussen'             (:208)

3  VERBINDUNGSART  joinType = 'neutral'                                          (:215)
     prevIsTraufe UND nextIsTraufe        ->  cornerType 'innen' ? 'kehle' : 'grat'   (:216)
     (Traufe an GIEBEL, beliebige Folge)  ->  'ortgang'                               (:217)
     sonst                                ->  bleibt 'neutral'

4  ZAEHLUNG        innenEcken · aussenEcken · grate · kehlen · ortgaenge        (:224-228)
```

> ### `prevIsTraufe` heißt NICHT „Typ ist TRAUFE"
>
> *Gelesen in `:212-213`:*
>
> ```ts
> const prevIsTraufe = prevEdge?.type === 'TRAUFE' || prevEdge?.type === 'WALM' || prevEdge?.type === 'TEILWALM';
> ```
>
> **Drei Kantentypen zählen als „traufseitig": `TRAUFE`, `WALM` und `TEILWALM`.** *Wer die Regel als
> „beide Kanten sind TRAUFE" liest, baut Walm- und Teilwalmdächer falsch — und das sind genau die
> Dächer, wegen derer es Grate gibt.*

> ### `neutral` ist kein Restfall, sondern der häufigste
>
> **Eine Regel, die nur drei von vier Ausgängen nennt, überlässt den vierten dem Bauenden.**
> *`neutral` gilt für **jede** Ecke ohne traufseitige Beteiligung — bei einem Satteldach sind das die
> beiden Giebelecken.* **Es ist kein Fehlerfall und darf keine Absage auslösen.**

## Ausgabe

```ts
// :222-229
{ points, edgeConfigs, corners,
  innenEcken, aussenEcken, grate, kehlen, ortgaenge }
```

*Je Ecke ein `TopologyCornerInfo` mit `angleDeg`, `cornerType` und `joinType` — **plus fünf
Zählungen**, die aus denselben Ecken abgeleitet sind.*

## Kommando (für Rückgängig)

**Keines.** *Die Analyse ist rein: Polygon und Kantentypen hinein, Ecken und Zählungen heraus. Sie
mutiert nichts.* **Ein Kommando braucht erst, wer aus dem Ergebnis Geometrie baut.**

## Schichtzuordnung

- **Schicht 1 (Domäne):** nein.
- **Schicht 2 (Geometrie):** **hierhin gehört sie** — `resources/planner/hausplaner/geometry/`.
- **Schicht 3 (Anwendung):** der Aufrufer ist W-07s Konturprüfung, siehe `7-GRENZEN.md`.
- **Schicht 4/5:** die Ecken lassen sich anzeigen (Grat/Kehle/Ortgang am Modell) — **nicht Gegenstand
  dieses Blattes.**
