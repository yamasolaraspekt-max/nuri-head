/**
 * Decke (Feature A) — REINE, testbare Geometrie-Helfer (kein three/WebGL). Der three-Aufsatz (szene.ts)
 * baut aus dem Decken-Polygon minus `oeffnungen` eine Shape-mit-Löchern auf der Wand-Oberkante. Hier nur
 * die belastbaren Kennwerte + die EINE Etagen-Stapel-Ableitung (eine Wahrheit, kein zweiter Rechenweg).
 */
import type { CeilingNode, Level } from '../../domain/scene.types';
import { polygonFlaecheM2 } from '../../geometry/polygonFlaeche';

/** Höhe der Decken-Unterkante = Wand-Oberkante des Levels (mm). */
export function deckenOberkanteMm(level: Pick<Level, 'elevation' | 'defaultWallHeight'>): number {
  return level.elevation + level.defaultWallHeight;
}

/** mm² pro m² — polygonFlaecheM2 rechnet KEINE Einheit um (Input in mm ⇒ Ergebnis mm²). */
const MM2_PRO_M2 = 1_000_000;

/** Netto-Deckenfläche (m²) = Umriss minus Durchbrüche (Treppenauge etc.). mm-Polygone → m². Nie negativ. */
export function deckenNettoFlaecheM2(ceiling: CeilingNode): number {
  const brutto = ceiling.polygon.length >= 3 ? Math.max(0, polygonFlaecheM2(ceiling.polygon)) : 0;
  const loch = (ceiling.oeffnungen ?? []).reduce(
    (s, o) => s + (o.polygon.length >= 3 ? Math.max(0, polygonFlaecheM2(o.polygon)) : 0),
    0,
  );
  return Math.max(0, brutto - loch) / MM2_PRO_M2;
}

/**
 * Etagen-Stapel (eine Wahrheit): Elevation der NÄCHSTEN Etage = untere Elevation + Wandhöhe + Deckendicke.
 * Fehlt die Decke, wird `level.floorThickness` als Deckendicke angesetzt (dokumentierter Rückfall, kein
 * Rateswert der Höhe selbst). Ganzzahlig (mm-Invariante).
 */
export function naechsteEtageElevationMm(
  level: Pick<Level, 'elevation' | 'defaultWallHeight' | 'floorThickness'>,
  decke: CeilingNode | undefined,
): number {
  const deckeDickeMm = decke ? decke.dickeMm : level.floorThickness;
  return Math.round(level.elevation + level.defaultWallHeight + deckeDickeMm);
}
