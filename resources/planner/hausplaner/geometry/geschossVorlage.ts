/**
 * Hausplaner — Geschoss als Vorlage duplizieren (Reuse-Pfeiler, klein & ohne Risiko).
 *
 * Reine Funktion: aus einem Quell-Geschoss + seinen Nodes (+ optional Dach) entsteht ein
 * neues Geschoss darüber, mit KOPIERTER Geometrie. Alle Nodes bekommen neue IDs; Öffnungen
 * werden auf die NEUEN Wand-IDs umgehängt (id-Remap), damit Türen/Fenster an ihren kopierten
 * Wänden hängen — nicht an den alten. Kein Schreibpfad, keine Szene-Mutation; das Ergebnis
 * füttert die Commands (ADD_LEVEL + ADD_NODE + ADD_ROOF).
 */

export interface LevelVorlage {
  id: string;
  name: string;
  elevation: number;
  defaultWallHeight: number;
  floorThickness: number;
  sortOrder: number;
}

interface NodeBasis {
  id: string;
  levelId: string;
  type: string;
  hostWallId?: string;
}

interface RoofBasis {
  id: string;
  levelId: string;
}

export interface GeschossDuplikat<N extends NodeBasis, R extends RoofBasis> {
  level: LevelVorlage;
  nodes: N[];
  roof: R | null;
}

/**
 * Dupliziert `quelle` (Geschoss) samt `nodes`/`roof` in ein neues Geschoss DARÜBER.
 * `neueId` liefert frische IDs (z. B. uuid); `neuerName` benennt das neue Geschoss.
 * Neue Höhenlage = elevation + defaultWallHeight + floorThickness (ein Stockwerk höher).
 */
export function dupliziereGeschoss<N extends NodeBasis, R extends RoofBasis>(
  quelle: LevelVorlage,
  nodes: ReadonlyArray<N>,
  roof: R | null,
  neueId: () => string,
  neuerName: string,
): GeschossDuplikat<N, R> {
  const neuesLevelId = neueId();
  const level: LevelVorlage = {
    id: neuesLevelId,
    name: neuerName,
    elevation: quelle.elevation + quelle.defaultWallHeight + quelle.floorThickness,
    defaultWallHeight: quelle.defaultWallHeight,
    floorThickness: quelle.floorThickness,
    sortOrder: quelle.sortOrder + 1,
  };

  const idMap = new Map<string, string>();
  for (const n of nodes) {
    idMap.set(n.id, neueId());
  }

  const neueNodes = nodes.map((n) => {
    const kopie = { ...n, id: idMap.get(n.id) as string, levelId: neuesLevelId } as N;
    if (n.hostWallId !== undefined) {
      kopie.hostWallId = idMap.get(n.hostWallId) ?? n.hostWallId;
    }
    return kopie;
  });

  const neuesDach = roof ? ({ ...roof, id: neueId(), levelId: neuesLevelId } as R) : null;

  return { level, nodes: neueNodes, roof: neuesDach };
}
