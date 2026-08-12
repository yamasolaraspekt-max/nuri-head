/**
 * W-27/1 — **Dachkanten-Topologie: was an einer ECKE des Grundrisses entsteht.**
 *
 * ## Die Grenze, die dieser Dateikopf sichtbar machen muss
 *
 * Die Wörter `'kehle'` und `'grat'` gibt es in dieser Insel **zweimal**, und sie beantworten
 * **verschiedene Fragen**:
 *
 * - `geometry/schifterListe.ts` → `klassifiziereSchifter(vStart, vEnd, vMax, tol)` fragt, ob ein
 *   **STAB** angeschnitten ist: erreicht er die Traufe nicht, heißt der Schnitt `'kehle'`;
 *   erreicht er den First nicht, `'grat'`. Das ist eine **Sparren**-Klassifikation.
 * - **Diese Datei** fragt, was an einer **ECKE des Grundrisses** entsteht, wenn zwei Kanten
 *   aufeinandertreffen. Das ist eine **Ecken**-Klassifikation.
 *
 * **Gleiche Wörter, andere Sache.** Wer hier eine Sparrenfrage beantwortet sucht, ist in der
 * falschen Datei — und umgekehrt. Diese Grenze steht hier, weil ein Leser, der `'kehle'` an zwei
 * Orten findet, an beiden erkennen können muss, welche Frage dort beantwortet wird.
 *
 * ## Kante gegen Ecke — der tragende Unterschied
 *
 * `EdgeTopologyType` beschreibt eine **KANTE**. `TopologyJoinType` beschreibt, was an einer **ECKE**
 * entsteht. **First und Grat sind keine Kantentypen** — sie entstehen *zwischen* zwei Kanten. Wer
 * sie als Kantentyp baut, baut die Sache falsch.
 *
 * ## Herkunft
 *
 * Übernommen aus dem Prototyp `docs/planner/pv-belegung-referenz/DachplanerProPage.tsx`:
 * `:85-87` die drei Typlisten, `:123` `TopologyPoint`, `:128` `EdgeTopologyConfig`,
 * `:135` `TopologyCornerInfo`, `:143` `TopologyAnalysis`, `:193-230` die Funktion.
 * Vorgabe: W-27s sieben Werkbankblätter (BETRIEBSBESTAETIGT).
 */

/** Ein Punkt des Grundriss-Polygons. Prototyp `:123`. */
export interface TopologyPoint {
  x: number;
  y: number;
}

/** Was eine KANTE ist. Prototyp `:85`. */
export type EdgeTopologyType = 'TRAUFE' | 'GIEBEL' | 'PULT_WAND' | 'WALM' | 'TEILWALM';

/** Was eine ECKE ist — innen oder außen. Prototyp `:86`. */
export type TopologyCornerType = 'innen' | 'aussen';

/**
 * Was an einer ECKE entsteht. Prototyp `:87`.
 *
 * **`'neutral'` ist der Default und nicht der Restfall** — jede Ecke bekommt ihn zugewiesen, bevor
 * die beiden Sonderfälle geprüft werden. Ohne ihn lieferte die Funktion `undefined`.
 */
export type TopologyJoinType = 'grat' | 'kehle' | 'ortgang' | 'neutral';

/** Die Konfiguration einer Kante. Prototyp `:128`. */
export interface EdgeTopologyConfig {
  id: string;
  type: EdgeTopologyType;
  pitch: number;
  label: string;
}

/** Das Ergebnis für eine einzelne Ecke. Prototyp `:135`. */
export interface TopologyCornerInfo {
  index: number;
  point: TopologyPoint;
  angleDeg: number;
  cornerType: TopologyCornerType;
  joinType: TopologyJoinType;
}

/**
 * Das vollständige Ergebnis. Prototyp `:143`.
 *
 * **Die fünf Zählungen sind der Grund, warum diese Struktur und nicht `TopologyCornerInfo[]`
 * zurückgegeben wird:** W-27s Ablesung hat als Lücke gemessen, dass `joinType` und `cornerType` im
 * Bestand je **0** Vorkommen haben. Genau diese Zahlen liefert die Analyse jetzt.
 */
export interface TopologyAnalysis {
  points: TopologyPoint[];
  edgeConfigs: EdgeTopologyConfig[];
  corners: TopologyCornerInfo[];
  innenEcken: number;
  aussenEcken: number;
  grate: number;
  kehlen: number;
  ortgaenge: number;
}

/**
 * Eine Kante, die eine **Traufe im weiteren Sinn** ist — also eine, an der ein Dach herunterläuft.
 *
 * **`WALM` und `TEILWALM` zählen mit**, und das ist keine Bequemlichkeit: genau an Walm- und
 * Teilwalmdächern entstehen die Grate, wegen derer es diese Klassifikation überhaupt gibt. Wer
 * `prevIsTraufe` als „die Kante ist `TRAUFE`" liest, baut sie falsch. Prototyp `:212-213`.
 */
function istTraufeImWeiterenSinn(edge: EdgeTopologyConfig | undefined): boolean {
  return edge?.type === 'TRAUFE' || edge?.type === 'WALM' || edge?.type === 'TEILWALM';
}

/**
 * **Schritt 0 — der Umlaufsinn.** Prototyp `:194-195`.
 *
 * Ohne ihn klassifiziert die Analyse bei umgekehrt gezeichnetem Polygon **alle** Ecken falsch
 * herum, und zwar **leise**: es gibt kein Ergebnis, das ungültig aussieht. Deshalb ist er ein
 * eigener, benannter Schritt und kein Nebeneffekt.
 */
function istGegenUhrzeigersinn(points: readonly TopologyPoint[]): boolean {
  const signedArea = points.reduce((acc, p, i) => {
    const n = points[(i + 1) % points.length];
    return acc + p.x * n.y - n.x * p.y;
  }, 0) / 2;
  return signedArea > 0;
}

/**
 * Klassifiziert jede Ecke eines Grundriss-Polygons.
 *
 * Vier Schritte, in dieser Reihenfolge:
 * 1. **Umlaufsinn** — ohne ihn kippt die Innen/Außen-Unterscheidung lautlos
 * 2. **Eckenwinkel** — `acos` des Skalarprodukts, bei einspringenden Ecken auf `360 − x` gespiegelt
 * 3. **Eckenart** — `> 180°` ist innen
 * 4. **Verbindungsart** — `'neutral'` als Default, dann Grat/Kehle, dann Ortgang
 */
export function analyzeTopology(
  points: TopologyPoint[],
  edgeConfigs: EdgeTopologyConfig[],
): TopologyAnalysis {
  const isCCW = istGegenUhrzeigersinn(points);

  const corners: TopologyCornerInfo[] = points.map((point, index) => {
    const prev = points[(index - 1 + points.length) % points.length];
    const next = points[(index + 1) % points.length];

    // Schritt 1 — Eckenwinkel. Die beiden Kantenrichtungen, auf Länge 1 gebracht.
    const v1x = prev.x - point.x;
    const v1y = prev.y - point.y;
    const v2x = next.x - point.x;
    const v2y = next.y - point.y;
    const l1 = Math.hypot(v1x, v1y) || 1;
    const l2 = Math.hypot(v2x, v2y) || 1;
    const n1x = v1x / l1;
    const n1y = v1y / l1;
    const n2x = v2x / l2;
    const n2y = v2y / l2;
    // Auf [-1, 1] geklemmt: Rundungsfehler jenseits davon lassen acos NaN liefern.
    const dot = Math.max(-1, Math.min(1, n1x * n2x + n1y * n2y));
    const baseAngle = (Math.acos(dot) * 180) / Math.PI;
    const cross = n1x * n2y - n1y * n2x;
    const isInnerReflex = isCCW ? cross > 0 : cross < 0;
    const angleDeg = isInnerReflex ? 360 - baseAngle : baseAngle;

    // Schritt 2 — Eckenart.
    const cornerType: TopologyCornerType = angleDeg > 180 ? 'innen' : 'aussen';

    // Schritt 3 — Verbindungsart. Die beiden Kanten, die an dieser Ecke zusammenstoßen.
    const prevEdge = edgeConfigs[(index - 1 + edgeConfigs.length) % edgeConfigs.length];
    const nextEdge = edgeConfigs[index % edgeConfigs.length];
    const prevIsTraufe = istTraufeImWeiterenSinn(prevEdge);
    const nextIsTraufe = istTraufeImWeiterenSinn(nextEdge);

    let joinType: TopologyJoinType = 'neutral';
    if (prevIsTraufe && nextIsTraufe) {
      joinType = cornerType === 'innen' ? 'kehle' : 'grat';
    } else if (
      (prevIsTraufe && nextEdge?.type === 'GIEBEL')
      || (nextIsTraufe && prevEdge?.type === 'GIEBEL')
    ) {
      joinType = 'ortgang';
    }

    return { index, point, angleDeg, cornerType, joinType };
  });

  return {
    points,
    edgeConfigs,
    corners,
    innenEcken: corners.filter((c) => c.cornerType === 'innen').length,
    aussenEcken: corners.filter((c) => c.cornerType === 'aussen').length,
    grate: corners.filter((c) => c.joinType === 'grat').length,
    kehlen: corners.filter((c) => c.joinType === 'kehle').length,
    ortgaenge: corners.filter((c) => c.joinType === 'ortgang').length,
  };
}
