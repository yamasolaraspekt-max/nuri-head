/**
 * Hausplaner — Maßketten (P2b-3). Reine Funktionen, mm-Integer-Welt.
 *
 * Eine Maßkette ist die Folge der Abstände zwischen benachbarten Bezugspunkten
 * entlang einer Achse — die Bemaßungslinie wie im Architekturplan. Der Renderer
 * LIEST das Ergebnis; hier wird nur gerechnet, nichts gezeichnet, nichts geschrieben.
 */

export interface MassPunkt {
  x: number;
  y: number;
}

export interface MassSegment {
  /** Position (mm) des linken/unteren Bezugspunkts. */
  von: number;
  /** Position (mm) des rechten/oberen Bezugspunkts. */
  bis: number;
  /** Abstand in mm (> 0). */
  laenge: number;
}

/**
 * Maßkette aus rohen Positionen: auf ganze mm runden, aufsteigend sortieren,
 * deduplizieren und aufeinanderfolgende Abstände bilden. Positionen, die näher als
 * `toleranz` mm beieinanderliegen, gelten als EIN Bezugspunkt (verhindert 0-Segmente,
 * z. B. wenn zwei Wände denselben Eckpunkt teilen).
 */
export function masskette(werte: ReadonlyArray<number>, toleranz = 1): MassSegment[] {
  const sortiert = werte.map((w) => Math.round(w)).sort((a, b) => a - b);
  const punkte: number[] = [];
  for (const w of sortiert) {
    if (punkte.length === 0 || w - punkte[punkte.length - 1] > toleranz) {
      punkte.push(w);
    }
  }

  const segmente: MassSegment[] = [];
  for (let i = 1; i < punkte.length; i++) {
    segmente.push({ von: punkte[i - 1], bis: punkte[i], laenge: punkte[i] - punkte[i - 1] });
  }
  return segmente;
}

export interface Bbox {
  minX: number;
  minY: number;
  maxX: number;
  maxY: number;
}

export interface GrundrissMassketten {
  /** Horizontale Bemaßung (Abstände entlang x, Maßlinie unter dem Gebäude). */
  xKette: MassSegment[];
  /** Vertikale Bemaßung (Abstände entlang y, Maßlinie links vom Gebäude). */
  yKette: MassSegment[];
  /** Gebäude-Umriss (Bounding-Box der Wandachsen); null ohne Wände. */
  bbox: Bbox | null;
}

interface WandLinie {
  start: MassPunkt;
  end: MassPunkt;
}

/**
 * Maßketten aus den Wand-Endpunkten eines Grundrisses: alle x-Positionen ergeben die
 * horizontale Kette, alle y-Positionen die vertikale. Bezugspunkte sind die
 * Wandachsen-Endpunkte (Öffnungs-Bemaßung ist eine spätere Ausbaustufe).
 */
export function grundrissMassketten(
  waende: ReadonlyArray<WandLinie>,
  toleranz = 1,
): GrundrissMassketten {
  const xs: number[] = [];
  const ys: number[] = [];
  for (const w of waende) {
    xs.push(w.start.x, w.end.x);
    ys.push(w.start.y, w.end.y);
  }
  if (xs.length === 0) {
    return { xKette: [], yKette: [], bbox: null };
  }

  const bbox: Bbox = {
    minX: Math.min(...xs),
    maxX: Math.max(...xs),
    minY: Math.min(...ys),
    maxY: Math.max(...ys),
  };

  return { xKette: masskette(xs, toleranz), yKette: masskette(ys, toleranz), bbox };
}
