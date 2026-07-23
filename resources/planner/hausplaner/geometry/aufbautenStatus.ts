/**
 * Reparatur 3: reine, testbare Statuslogik für Dach-Aufbauten (Kamin, Gaube,
 * Dachfenster, sonstige Hindernisse) des 3D-Planers.
 *
 * Aufbauten sind über eine Flächen-ID + relative Position (x,y in 0..1) an eine
 * Dachfläche gebunden. Bei einer Geometrieänderung werden sie nachgezogen
 * (updateObstacles bildet die relativen Koordinaten neu auf die aktuelle Flächen-
 * geometrie ab). Existiert die zugehörige Fläche nicht mehr (z. B. nach einem
 * Dachformwechsel), kann der Aufbau nicht sicher neu positioniert werden — dann
 * gilt er als PRÜFPFLICHTIG (nicht still löschen, nicht still falsch weiterführen).
 *
 * Diese Datei ist bewusst rein (keine React-/THREE-Abhängigkeit) und damit
 * vollständig per Unit-Test prüfbar. Sie ändert NICHTS an Reparatur 1
 * (dachWerte.ts) oder Reparatur 2 (belegungStatus.ts).
 */

export interface AufbauRef {
  id: string;
  surfaceId: string;
}

export interface AufbautenPruefErgebnis {
  /** IDs der Aufbauten, deren Dachfläche nach der Geometrieänderung nicht mehr existiert. */
  pruefpflichtigIds: string[];
  anzahl: number;
}

/** Standard-Warntext, wenn Aufbauten nach einer Geometrieänderung geprüft werden müssen. */
export const AUFBAUTEN_WARNUNG =
  "Die Dachgeometrie wurde geändert. Ein oder mehrere Aufbauten müssen neu geprüft werden.";

/**
 * Ermittelt die Aufbauten, die keiner aktuell vorhandenen Dachfläche mehr
 * zugeordnet werden können (Fläche existiert nicht mehr). Diese sind prüfpflichtig.
 * Aufbauten auf weiterhin vorhandenen Flächen werden korrekt nachgezogen und
 * sind NICHT prüfpflichtig.
 */
export function aufbautenOhneFlaeche(
  aufbauten: AufbauRef[],
  vorhandeneFlaechenIds: string[],
): AufbautenPruefErgebnis {
  const vorhanden = new Set(vorhandeneFlaechenIds);
  const pruefpflichtigIds = (aufbauten || [])
    .filter((a) => !vorhanden.has(a.surfaceId))
    .map((a) => a.id);
  return { pruefpflichtigIds, anzahl: pruefpflichtigIds.length };
}

/** true, wenn ein einzelner Aufbau (per ID) prüfpflichtig ist. */
export function istAufbauPruefpflichtig(id: string, pruefpflichtigIds: string[]): boolean {
  return Array.isArray(pruefpflichtigIds) && pruefpflichtigIds.includes(id);
}
