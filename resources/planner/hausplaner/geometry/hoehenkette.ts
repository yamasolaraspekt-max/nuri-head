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

// ---------------------------------------------------------------- Z1-E4-1: das untere Ende
//
// **Bis hierher rechnete die Kette nur nach oben.** `deckenOberkanteMm` und
// `naechsteEtageElevationMm` beantworten „wie hoch liegt das Geschoss darüber" — was UNTEN
// abschließt, kannte die Datei nicht. Die drei folgenden Funktionen ergänzen genau das, **additiv:
// keine der beiden bestehenden Funktionen ist angefasst.**

/** Eine Schicht des Fußbodenaufbaus — feldgleich mit `CeilingNode.schichten`/`FoundationSlabNode.schichten`. */
type Aufbauschicht = { materialId?: string; dickeMm: number };

/**
 * **Gesamtdicke des Fußbodenaufbaus über der tragenden Platte** (mm).
 *
 * Reihenfolgeunabhängig — es ist eine Summe (siehe `SCHICHT_REIHENFOLGE`: die Festlegung
 * außen→innen ordnet die Liste, sie ändert diese Rechnung nicht).
 */
export function fussbodenaufbauDickeMm(schichten: readonly Aufbauschicht[] | undefined): number {
  return (schichten ?? []).reduce((summe, s) => summe + s.dickeMm, 0);
}

/**
 * **Ist der Fußbodenaufbau überhaupt erfasst?**
 *
 * *Getrennte Frage von „wie dick ist er".* Ein nicht erfasster Aufbau und ein Aufbau der Dicke 0
 * ergeben beide die Summe 0 — aber nur der zweite ist eine Angabe. Das Panel muss das
 * unterscheiden können (Kriterium `Z1-E4-1-g`: **behaupte nichts, was nicht geprüft ist**).
 */
export function fussbodenaufbauErfasst(schichten: readonly Aufbauschicht[] | undefined): boolean {
  return (schichten ?? []).length > 0;
}

/**
 * **Oberkante der tragenden Bodenplatte** (mm), bezogen auf **±0,00 = OK Fertigfußboden EG**.
 *
 * **Yamas Operand 1 vom 22.08.2026, 22:08**, wörtlich: *„plus-minus-0,00 = OK FERTIGFUSSBODEN EG →
 * die Platte liegt um den GESAMTEN Fußbodenaufbau tiefer; `oberkanteMm` ist bei `erdberuehrt=true`
 * NEGATIV (Bauzeichnungs-Konvention)."*
 *
 * ```text
 *   ±0,00  ────────────────  OK Fertigfußboden
 *                    ↑ Estrich, Dämmung, … = fussbodenaufbauDickeMm
 *   -180   ────────────────  OK tragende Platte   ← dieser Rückgabewert
 *                    ↑ dickeMm der Platte
 * ```
 *
 * **Ist der Aufbau nicht erfasst, ist der Rückgabewert 0** — und 0 ist hier keine Messung, sondern
 * das Fehlen einer. *Deshalb steht daneben `fussbodenaufbauErfasst`, und deshalb schreibt das
 * Panel „Aufbau nicht erfasst" statt einer Höhe.* Eine erfundene Aufbaudicke wäre schlimmer als
 * keine — sie sähe aus wie eine Angabe.
 *
 * @param bezugshoeheMm Elevation des Geschosses, auf dem die Platte liegt. Für das EG ist das 0
 *        (±0,00 selbst); ein Keller mit eigener Sohle reicht seine eigene, negative Elevation
 *        herein. *Ohne diesen Parameter wäre die Funktion auf das EG festgelegt und Yamas
 *        Entscheidung „eine Platte je Geschoss" wäre nicht abbildbar.*
 */
export function bodenplatteOberkanteMm(
  schichten: readonly Aufbauschicht[] | undefined,
  bezugshoeheMm = 0,
): number {
  return Math.round(bezugshoeheMm - fussbodenaufbauDickeMm(schichten));
}
