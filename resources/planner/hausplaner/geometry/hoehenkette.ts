/**
 * **Z1-E0-1 — die Höhenkette ist EINE Wahrheit.**
 *
 * ---
 *
 * **Die Lücke L1, am Stand `fd2575ce` gemessen:** dieselbe Größe — die Elevation der nächsten
 * Etage — wurde an **drei** Stellen gerechnet, und die einzige, die es richtig machte, rief
 * niemand auf.
 *
 * ```text
 * deckenMesh.ts:37   naechsteEtageElevationMm   BERUECKSICHTIGT die Decke   Aufrufer 0
 * Kopfrahmen.tsx:172 „Geschoss anlegen"          + floorThickness           kennt die Decke nicht
 * geschossVorlage.ts:54 Duplizieren              + floorThickness           kennt die Decke nicht
 * ```
 *
 * **Das ist keine Ungenauigkeit, sondern eine zweite Wahrheit über die Höhenlage jeder Etage.**
 * Bei einem EG mit `floorThickness` 200 und einer Decke von 240 sagt die eine Rechnung `2700`,
 * die andere `2740`.
 *
 * **Diese Datei ist ab jetzt die einzige Stelle, die diese Größen erzeugt.** Die drei Rechenstellen
 * lesen daraus; keine von ihnen rechnet noch selbst. *Eine vierte Funktion neben den dreien wäre
 * keine Zusammenführung, sondern eine Vermehrung — das Blatt sagt es ausdrücklich.*
 *
 * **Warum `geometry/` und nicht `renderers/three-d/`:** die Höhenkette ist Fachgeometrie und
 * gehört keinem Darstellungsweg. Sie lag bisher unter `renderers/three-d/deckenMesh.ts` — dort,
 * wo sie ein 3D-Aufsatz gerade brauchte. *Ein Wert, den der Kopfrahmen und die Geschossvorlage
 * lesen müssen, kann nicht im Renderer wohnen.*
 */
import type { CeilingNode, Level } from '../domain/scene.types';

/**
 * Höhe der Decken-Unterkante = **Wand-Oberkante** des Levels (mm).
 *
 * Auch die Traufhöhe eines Dachs liest hier — `scene.types.ts:327` nennt sie als Vorgabe
 * „`level.elevation + defaultWallHeight`", und das ist genau diese Größe.
 */
export function deckenOberkanteMm(level: Pick<Level, 'elevation' | 'defaultWallHeight'>): number {
  return level.elevation + level.defaultWallHeight;
}

/**
 * **Elevation der NÄCHSTEN Etage** = untere Elevation + Wandhöhe + Deckendicke (mm, ganzzahlig).
 *
 * **Fehlt die Decke, tritt `level.floorThickness` an ihre Stelle** — ein dokumentierter Rückfall,
 * kein geratener Höhenwert. *Genau dieser Rückfall hält den Bestand bitgleich, solange niemand
 * eine Decke modelliert hat:* die beiden alten Rechnungen addierten `floorThickness`, und das tut
 * dieser Zweig auch.
 */
export function naechsteEtageElevationMm(
  level: Pick<Level, 'elevation' | 'defaultWallHeight' | 'floorThickness'>,
  // **Nur `dickeMm` wird gebraucht** — deshalb steht hier `Pick`, nicht der volle `CeilingNode`.
  // Sonst müsste `geschossVorlage.ts`, das mit einer schlanken `LevelVorlage` arbeitet, einen
  // Knoten hereinreichen, den es nicht hat — oder casten. *Ein `as` an der Aufrufstelle wäre
  // eine Umgehung des Vertrags, kein Erfüllen.*
  decke: Pick<CeilingNode, 'dickeMm'> | undefined,
): number {
  const deckeDickeMm = decke ? decke.dickeMm : level.floorThickness;
  return Math.round(deckenOberkanteMm(level) + deckeDickeMm);
}
