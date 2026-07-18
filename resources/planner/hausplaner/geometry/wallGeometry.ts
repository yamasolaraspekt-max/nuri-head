/**
 * Hausplaner — Wand-Geometrie (P0). Reine Funktionen, mm-Integer-Welt.
 * Azimut-Konvention (Spec ▲K2, dokumentiert): Nord = +y; Azimut = Richtung der
 * Wand-NORMALEN, im Uhrzeigersinn von Nord, ganzzahlig 0–359.
 */

export interface Punkt {
  x: number;
  y: number;
}

/** Wandlänge in mm (Gleitkomma für Vergleiche; Persistenz bleibt ganzzahlig). */
export function wandLaenge(start: Punkt, end: Punkt): number {
  return Math.hypot(end.x - start.x, end.y - start.y);
}

/** Punkt auf der Wandachse bei offset (mm) ab start — ganzzahlig gerundet. */
export function punktAufWand(start: Punkt, end: Punkt, offset: number): Punkt {
  const laenge = wandLaenge(start, end);
  if (laenge === 0) {
    return { x: start.x, y: start.y };
  }
  const t = offset / laenge;

  return {
    x: Math.round(start.x + (end.x - start.x) * t),
    y: Math.round(start.y + (end.y - start.y) * t),
  };
}

/**
 * Azimut der Wand-Normalen (Grad, 0–359, ganzzahlig). `seite` wählt die Normale:
 * 'links' = 90° gegen den Uhrzeigersinn zur Laufrichtung start→end, 'rechts' = im Uhrzeigersinn.
 * Beispiele (seite='links'): Wand West→Ost (+x) ⇒ Normale +y ⇒ 0° (Nord);
 * Wand Süd→Nord (+y) ⇒ Normale −x ⇒ 270° (West).
 */
export function azimutDerNormalen(start: Punkt, end: Punkt, seite: 'links' | 'rechts'): number {
  const dx = end.x - start.x;
  const dy = end.y - start.y;
  // Normale: links = (-dy, dx) gedreht… in Bildschirm-Mathematik mit y nach OBEN (Nord=+y):
  // Laufrichtung (dx,dy); linke Normale = (-dy, dx); rechte = (dy, -dx).
  const nx = seite === 'links' ? -dy : dy;
  const ny = seite === 'links' ? dx : -dx;

  // Azimut im Uhrzeigersinn von Nord (+y): atan2(ost-Anteil, nord-Anteil).
  const rad = Math.atan2(nx, ny);
  const grad = Math.round((rad * 180) / Math.PI);

  return ((grad % 360) + 360) % 360;
}

/** Prüft die mm-Invariante eines Punkts (ganzzahlig). */
export function istGanzzahlig(p: Punkt): boolean {
  return Number.isInteger(p.x) && Number.isInteger(p.y);
}
