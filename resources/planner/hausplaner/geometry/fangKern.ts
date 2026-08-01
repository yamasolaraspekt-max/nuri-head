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


/**
 * **Der Fangradius in BILDSCHIRM-Pixeln** — und das ist der ganze Befund von Z-02.
 *
 * Bis heute stand im Bauteil ein fester Radius von 150 **Millimetern**. Millimeter sind
 * Weltkoordinaten; wie viel davon der Mensch am Schirm sieht, hängt am Zoom:
 *
 * ```text
 * Zoom (px pro mm)   150 mm entsprechen        12 px entsprechen
 *   0.02                   3 Bildschirmpixel        600 mm
 *   0.12 (Standard)       18 Bildschirmpixel        100 mm
 *   0.50                  75 Bildschirmpixel         24 mm
 * ```
 *
 * *Bei 0,02 war der Fang praktisch tot — drei Pixel trifft niemand. Bei 0,5 riss er den Zeiger
 * aus 75 Pixeln Entfernung an sich.* **Ein Radius, der sich nicht mit dem Zoom ändert, ist
 * entweder unbrauchbar oder aufdringlich, nie beides richtig.**
 */
export const FANG_PX = 12;

/**
 * **Bildschirm-Pixel in Welt-Millimeter, über den Zoom.**
 *
 * *Warum das hier steht und nicht im Bauteil:* die Umrechnung ist der Kern der Entscheidung von
 * Z-02, und im Rumpf einer React-Funktion wäre sie von keiner Zusage erreichbar. Hier ist sie
 * eine reine Funktion — prüfbar, ohne den Planer zu starten.
 *
 * **Der Wächter gegen `zoom = 0`:** eine Division durch null ergäbe `Infinity`, und eine
 * unendliche Toleranz fängt **jeden** Punkt auf den nächstbesten Endpunkt. Das sähe nicht nach
 * einem Fehler aus, sondern nach einem sehr eifrigen Fang. Deshalb fällt die Funktion auf den
 * Pixelwert zurück, statt still etwas Unsinniges zu liefern.
 */
export function toleranzAusZoom(zoom: number, fangPx: number = FANG_PX): number {
  if (!(zoom > 0)) {
    return fangPx;
  }

  return fangPx / zoom;
}

/** Eine Wandstrecke (für die Fangpunkt-Sammlung). */
export interface WandStrecke {
  start: FangPunkt;
  end: FangPunkt;
}

/**
 * Sammelt die Fang-Kandidaten aus den Wänden: Endpunkte + Mittelpunkt je Wand. Reine Ableitung —
 * der Aufrufer reicht das Ergebnis an fange(); so bleibt fange() geometriefrei und testbar.
 */
export function wandFangpunkte(waende: ReadonlyArray<WandStrecke>): FangPunkt[] {
  const pts: FangPunkt[] = [];
  for (const w of waende) {
    pts.push({ x: w.start.x, y: w.start.y });
    pts.push({ x: w.end.x, y: w.end.y });
    pts.push({ x: Math.round((w.start.x + w.end.x) / 2), y: Math.round((w.start.y + w.end.y) / 2) });
  }
  return pts;
}
