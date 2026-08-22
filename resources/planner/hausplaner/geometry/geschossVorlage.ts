import { naechsteEtageElevationMm } from './hoehenkette';
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

/**
 * Z1-E2-1 — was ein Bauteil mitbringen muss, damit es beim Duplizieren mitwandern kann:
 * eine Id, die neu vergeben wird, und eine `levelId`, die umgehängt wird.
 *
 * **Eigener Name, obwohl die Felder dieselben sind wie in `RoofBasis`.** Eine Decke über
 * `RoofBasis` zu binden würde übersetzen und wäre gelogen — der Typ hieße Dach und meinte Decke.
 */
interface EbenenBauteil {
  id: string;
  levelId: string;
}

export interface GeschossDuplikat<N extends NodeBasis, R extends RoofBasis, C extends EbenenBauteil = EbenenBauteil> {
  level: LevelVorlage;
  nodes: N[];
  roof: R | null;
  /** Z1-E2-1: die mitkopierte Decke, oder `null` wenn das Quellgeschoss keine hatte. */
  ceiling: C | null;
}

/**
 * Dupliziert `quelle` (Geschoss) samt `nodes`/`roof` in ein neues Geschoss DARÜBER.
 * `neueId` liefert frische IDs (z. B. uuid); `neuerName` benennt das neue Geschoss.
 * Neue Höhenlage: **Z1-E0-1 — die Höhenkette rechnet sie**, nicht diese Datei. Wird `decke`
 * übergeben, geht ihre Dicke ein; ohne sie bleibt es bei `floorThickness` — bitgleich zum
 * bisherigen Verhalten.
 */
export function dupliziereGeschoss<N extends NodeBasis, R extends RoofBasis, C extends EbenenBauteil = EbenenBauteil>(
  quelle: LevelVorlage,
  nodes: ReadonlyArray<N>,
  roof: R | null,
  neueId: () => string,
  neuerName: string,
  /** Z1-E0-1, additiv: die Decke des Quellgeschosses. Fehlt sie, gilt `floorThickness`. */
  decke?: { dickeMm: number },
  /** Z1-E2-1, additiv: dieselbe Decke als Knoten — sie wird mitkopiert wie das Dach. */
  decke2?: C,
): GeschossDuplikat<N, R, C> {
  const neuesLevelId = neueId();
  const level: LevelVorlage = {
    id: neuesLevelId,
    name: neuerName,
    elevation: naechsteEtageElevationMm(quelle, decke),
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
      // Härtung (Evaluator): hängende Wirtswand (nicht mitkopiert) NICHT auf die alte id zeigen
      // lassen — das bände die Öffnung an eine Wand des Quell-Geschosses. Referenz droppen (undefined).
      kopie.hostWallId = idMap.get(n.hostWallId);
    }
    return kopie;
  });

  const neuesDach = roof ? ({ ...roof, id: neueId(), levelId: neuesLevelId } as R) : null;
  // Z1-E2-1: die Decke geht denselben Weg wie das Dach — neue Id, neue levelId. Bis hierher
  // kannte diese Funktion nur `roof`, und das duplizierte Geschoss stand ohne Decke da.
  const neueDecke = decke2 ? ({ ...decke2, id: neueId(), levelId: neuesLevelId } as C) : null;

  return { level, nodes: neueNodes, roof: neuesDach, ceiling: neueDecke };
}
