/**
 * Hausplaner L3b — Fang-Kern (Snapping), reine Funktion ohne Konva/three.
 *
 * Bestimmt für einen Rohpunkt den gefangenen Punkt + die Fang-Art. Priorität:
 * Endpunkt (nächster Bezugspunkt in Toleranz) > Ortho (0/90° zu einem Referenzpunkt) > Raster.
 * „Eine Wahrheit": es gibt genau EINE Fang-Entscheidung; der Aufrufer rechnet Bildschirm-px über
 * den Zoom in mm-Toleranz um und rendert den Indikator nach `art`. Nie stilles Fangen: `art`
 * benennt, WAS gefangen wurde (oder 'keiner').
 */

export type FangArt = 'endpunkt' | 'ortho' | 'raster' | 'keiner';

export interface FangPunkt {
  x: number;
  y: number;
}

export interface FangOptionen {
  /** Endpunkt-Toleranz in mm (Aufrufer: px→mm via Zoom). */
  toleranzMm: number;
  /** Rasterweite in mm (0/undefined = kein Raster). */
  raster?: number;
  /** Referenzpunkt für Ortho (z. B. Wandanfang). Ohne diesen kein Ortho-Fang. */
  ortho?: FangPunkt;
  /** Achs-Toleranz für Ortho in mm (Default = toleranzMm·2). */
  orthoToleranzMm?: number;
  /** Fang global an? (entspricht settings.snapEnabled). Default true. */
  aktiv?: boolean;
}

export interface FangErgebnis {
  punkt: FangPunkt;
  art: FangArt;
}

const runde = (p: FangPunkt): FangPunkt => ({ x: Math.round(p.x), y: Math.round(p.y) });

export function fange(
  p: FangPunkt,
  kandidaten: ReadonlyArray<FangPunkt>,
  opt: FangOptionen,
): FangErgebnis {
  if (opt.aktiv === false) {
    return { punkt: runde(p), art: 'keiner' };
  }

  // 1) Endpunkt — nächster Kandidat innerhalb Toleranz gewinnt.
  let besterAbstand = Infinity;
  let bester: FangPunkt | null = null;
  for (const k of kandidaten) {
    const d = Math.hypot(p.x - k.x, p.y - k.y);
    if (d <= opt.toleranzMm && d < besterAbstand) {
      besterAbstand = d;
      bester = k;
    }
  }
  if (bester) {
    return { punkt: runde(bester), art: 'endpunkt' };
  }

  // 2) Ortho — nahe einer Achse durch den Referenzpunkt (0/90°).
  if (opt.ortho) {
    const otol = opt.orthoToleranzMm ?? opt.toleranzMm * 2;
    const dx = p.x - opt.ortho.x;
    const dy = p.y - opt.ortho.y;
    // näher an der Waagerechten (kleines |dy|) ⇒ y auf Referenz; sonst Senkrechte ⇒ x auf Referenz.
    if (Math.abs(dy) <= otol && Math.abs(dy) <= Math.abs(dx)) {
      return { punkt: runde({ x: p.x, y: opt.ortho.y }), art: 'ortho' };
    }
    if (Math.abs(dx) <= otol && Math.abs(dx) < Math.abs(dy)) {
      return { punkt: runde({ x: opt.ortho.x, y: p.y }), art: 'ortho' };
    }
  }

  // 3) Raster — auf die nächste Rasterlinie runden.
  if (opt.raster && opt.raster > 0) {
    const r = opt.raster;
    return { punkt: { x: Math.round(p.x / r) * r, y: Math.round(p.y / r) * r }, art: 'raster' };
  }

  return { punkt: runde(p), art: 'keiner' };
}
