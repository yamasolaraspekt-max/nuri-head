/**
 * Hausplaner — Professionelle Bemaßung (mehrstufige Maßketten), reine Funktion.
 *
 * Ersetzt das naive „alle Bandecken in eine Kette"-Verfahren (das an Ecken Artefakt-Striche
 * erzeugte) durch SAUBER definierte Referenzpunkte je Achse und mehrere parallele Ketten wie in
 * einer echten Bauzeichnung:
 *   - innen die ÖFFNUNGS-/ROHBAUKETTE: Wandstärken + Fenster-/Tür-Öffnungen + lichte Maße,
 *   - außen das GESAMT-AUSSENMASS: Außenkante → Außenkante (ein Maß).
 *
 * Referenzpunkte (achsenorientierter Grundriss):
 *   X-Achse (Maßlinie unter dem Gebäude): die beiden Flächen jeder SENKRECHTEN Wand (x = Achse ±
 *     Dicke/2) plus die X-Kanten von Öffnungen auf WAAGERECHTEN Wänden.
 *   Y-Achse (links): die Flächen jeder WAAGERECHTEN Wand plus die Y-Kanten von Öffnungen auf
 *     senkrechten Wänden.
 * Damit erscheinen Wandstärken und Öffnungen als echte Maße — ohne Ecken-Artefakte.
 */

import { masskette, type MassSegment, type Bbox } from './masskette';

export interface BemPunkt {
  x: number;
  y: number;
}
export interface BemWand {
  id: string;
  start: BemPunkt;
  end: BemPunkt;
  thickness: number;
}
export interface BemOeffnung {
  hostWallId: string;
  offsetFromWallStart: number;
  width: number;
}

export interface AchsKetten {
  /** innere Kette: Wandstärken + Öffnungen + lichte Maße. */
  oeffnung: MassSegment[];
  /** äußeres Gesamtmaß (Außenkante–Außenkante); null ohne Bezugspunkte. */
  gesamt: MassSegment | null;
}

export interface Bemassung {
  x: AchsKetten;
  y: AchsKetten;
  bbox: Bbox | null;
}

const istWaagerecht = (w: BemWand): boolean =>
  Math.abs(w.end.x - w.start.x) >= Math.abs(w.end.y - w.start.y);

export function bemassung(
  waende: ReadonlyArray<BemWand>,
  oeffnungen: ReadonlyArray<BemOeffnung>,
  toleranz = 1,
): Bemassung {
  const refX: number[] = [];
  const refY: number[] = [];
  const wandById = new Map(waende.map((w) => [w.id, w]));

  for (const w of waende) {
    const t = w.thickness / 2;
    if (istWaagerecht(w)) {
      const cy = (w.start.y + w.end.y) / 2;
      refY.push(cy - t, cy + t); // Flächen der waagerechten Wand liegen in Y
    } else {
      const cx = (w.start.x + w.end.x) / 2;
      refX.push(cx - t, cx + t); // Flächen der senkrechten Wand liegen in X
    }
  }

  for (const o of oeffnungen) {
    const w = wandById.get(o.hostWallId);
    if (!w) continue;
    const dx = w.end.x - w.start.x;
    const dy = w.end.y - w.start.y;
    const len = Math.hypot(dx, dy);
    if (len === 0) continue;
    const ux = dx / len;
    const uy = dy / len;
    const a = { x: w.start.x + ux * o.offsetFromWallStart, y: w.start.y + uy * o.offsetFromWallStart };
    const b = { x: w.start.x + ux * (o.offsetFromWallStart + o.width), y: w.start.y + uy * (o.offsetFromWallStart + o.width) };
    if (istWaagerecht(w)) {
      refX.push(a.x, b.x); // Öffnung auf waagerechter Wand → X-Kanten
    } else {
      refY.push(a.y, b.y); // Öffnung auf senkrechter Wand → Y-Kanten
    }
  }

  const achse = (ref: number[]): AchsKetten => {
    if (ref.length === 0) return { oeffnung: [], gesamt: null };
    const min = Math.round(Math.min(...ref));
    const max = Math.round(Math.max(...ref));
    return {
      oeffnung: masskette(ref, toleranz),
      gesamt: max > min ? { von: min, bis: max, laenge: max - min } : null,
    };
  };

  const x = achse(refX);
  const y = achse(refY);
  const bbox: Bbox | null =
    x.gesamt && y.gesamt
      ? { minX: x.gesamt.von, maxX: x.gesamt.bis, minY: y.gesamt.von, maxY: y.gesamt.bis }
      : null;

  return { x, y, bbox };
}
