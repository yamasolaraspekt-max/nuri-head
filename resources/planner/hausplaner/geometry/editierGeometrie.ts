/**
 * Hausplaner — Editier-Geometrie (Bewegen / Duplizieren / Spiegeln). Reine Funktionen,
 * mm-Integer-Welt. Kein Rendern, keine Szene-Mutation — nur Koordinaten-Rechnung, die
 * die Commands (MOVE_NODE / ADD_NODE / UPDATE_NODE) füttert.
 */

export interface Punkt {
  x: number;
  y: number;
}

export type Achse = 'vertikal' | 'horizontal';

/** Punkt um (dx,dy) versetzen, ganzzahlig. */
export function versetzePunkt(p: Punkt, dx: number, dy: number): Punkt {
  return { x: Math.round(p.x + dx), y: Math.round(p.y + dy) };
}

/** Wand-Endpunkte um (dx,dy) versetzen (bewegen/duplizieren mit Versatz). */
export function versetzteWand(
  start: Punkt,
  end: Punkt,
  dx: number,
  dy: number,
): { start: Punkt; end: Punkt } {
  return { start: versetzePunkt(start, dx, dy), end: versetzePunkt(end, dx, dy) };
}

/**
 * Punkt an einer Achse spiegeln.
 * - 'vertikal'   = Spiegelachse ist senkrecht (x = pos) ⇒ x wird gespiegelt, y bleibt.
 * - 'horizontal' = Spiegelachse ist waagerecht (y = pos) ⇒ y wird gespiegelt, x bleibt.
 */
export function spiegelePunkt(p: Punkt, achse: Achse, pos: number): Punkt {
  if (achse === 'vertikal') {
    return { x: Math.round(2 * pos - p.x), y: Math.round(p.y) };
  }
  return { x: Math.round(p.x), y: Math.round(2 * pos - p.y) };
}

/**
 * Wand an einer Achse spiegeln. Beide Endpunkte werden gespiegelt; die Wand behält
 * dadurch ihre Lage relativ zur Achse, ihre Laufrichtung kehrt sich um (unkritisch,
 * da Länge/Dicke gleich bleiben).
 */
export function spiegelteWand(
  start: Punkt,
  end: Punkt,
  achse: Achse,
  pos: number,
): { start: Punkt; end: Punkt } {
  return { start: spiegelePunkt(start, achse, pos), end: spiegelePunkt(end, achse, pos) };
}

export interface Bbox {
  minX: number;
  minY: number;
  maxX: number;
  maxY: number;
}

/** Bounding-Box einer Punktmenge (für Spiegelachse durch die Auswahl-Mitte). */
export function bbox(punkte: ReadonlyArray<Punkt>): Bbox | null {
  if (punkte.length === 0) {
    return null;
  }
  const xs = punkte.map((p) => p.x);
  const ys = punkte.map((p) => p.y);
  return { minX: Math.min(...xs), maxX: Math.max(...xs), minY: Math.min(...ys), maxY: Math.max(...ys) };
}

/** Mittelachsen-Position einer Bbox für eine Spiegelachse. */
export function achsenMitte(b: Bbox, achse: Achse): number {
  return achse === 'vertikal' ? (b.minX + b.maxX) / 2 : (b.minY + b.maxY) / 2;
}
