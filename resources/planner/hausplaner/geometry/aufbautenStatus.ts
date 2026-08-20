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
  *
 * ---
 *
 * **BEFUND 20.08. — die Lage, die dieses Modul erkennt, kann in DIESER Domaene nicht eintreten.**
 *
 * `aufbautenOhneFlaeche` sucht Aufbauten, deren `surfaceId` in den vorhandenen Flaechen fehlt.
 * Beide Haelften dieser Bindung sind hier anders gebaut, gemessen:
 *
 *   RoofAufbau (domain/scene.types.ts)   id, typ, x, y, breiteMm, hoeheMm, tiefeMm, …
 *                                        -> **keine surfaceId, keine Flaechenbindung** (0 Treffer)
 *   surfaceId im Schema                  domain/validation.ts 0 · scene-document-v2.schema.json 0
 *   Flaechen-Kennungen                   renderers/three-d/dachMesh.ts:196/243ff, abgeleitet als
 *                                        `${roof.id}#0`, `#1`, … — **erst beim Zeichnen**,
 *                                        aufgerufen von szene.ts:522, nirgends gespeichert
 *
 * **Ein Aufbau kann seine Flaeche nicht verlieren, weil er nie eine gespeicherte hatte.** Er haengt
 * am RoofNode mit relativer Lage (x, y in 0..1); aendert sich die Dachform, werden die Flaechen neu
 * abgeleitet und die relative Lage neu ausgelegt. Es gibt keinen Zustand, in dem `pruefpflichtigIds`
 * nicht leer waere.
 *
 * **Das ist KEINE Fachfrage** — die Fach-Linse hat an der Logik nichts zu beanstanden und nennt das
 * Modul als das einzige der Dach-Gruppe ohne offene Fachentscheidung. Es ist eine **Modellfrage:**
 * anschliessbar wird es erst, wenn `RoofAufbau` eine Flaechenbindung traegt und Flaechen eine
 * gespeicherte Kennung haben. Beides ist eine additive Schema-Aenderung und gehoert Yama, nicht
 * einem Anschluss-Auftrag.
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
