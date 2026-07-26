/**
 * Hausplaner — Treppen-SVG (maßstäbliche Grundriss-Zeichnung, wie kalk.pro-Prinzip).
 *
 * Nimmt eine typ-neutrale Treppen-Zeichnung (Umriss + Trittstufen-Linien + Lauflinie + Aufwärts-
 * Pfeil, alles in mm, Nord=+y) und rendert eine saubere, skalierte SVG-Grundrisszeichnung mit
 * Rahmen, Stufennummern und Gesamtmaß. Rein: liefert einen SVG-String, keine DOM-/three-Abhängigkeit.
 * Damit funktioniert dieselbe Zeichnung für gerade, L-, U-, Spindel- und Wendeltreppen — jeder Typ
 * liefert nur seine Geometrie, das Rendern ist gemeinsam.
 */

export interface SvgP { x: number; y: number }

export interface TreppenZeichnung {
  /** Gesamt-Umriss (Polygon, mm). */
  umriss: SvgP[];
  /** Trittstufen-Grenzlinien (je [a,b], mm). */
  stufenlinien: Array<[SvgP, SvgP]>;
  /** Lauflinie (Polyline, mm) — Gehweg von unten nach oben. */
  lauflinie: SvgP[];
  /** Pfeilspitze (Aufwärtsrichtung), mm. */
  pfeilBis: SvgP;
}

/**
 * AUF-54 — **die sechs Farbrollen dieser Zeichnung, hereingereicht statt gekannt.**
 *
 * Hier stehen **Rollen, keine Werte**. Welcher Farbwert eine Rolle trägt, entscheidet die
 * aufrufende Schicht — `geometry/` ist reine Geometrie und hat über Aussehen nicht zu befinden.
 * Vorher lagen hier sechs rohe Werte, darunter ein drittes Grün für dieselbe Rolle, für die
 * `studioDaten.ts` bereits eines führt und `szene.ts` ein weiteres rendert.
 */
export interface TreppenFarben {
  /** Gesamt-Umriss der Treppe. */
  umriss: string;
  /** Trittstufen-Grenzlinien. */
  stufe: string;
  /** Lauflinie und Aufwärts-Pfeil. */
  lauflinie: string;
  /** Stufennummern, Titel, Gesamtmaß. */
  text: string;
  /** Rahmen der Zeichenfläche. */
  rahmen: string;
  /** Hintergrund der Zeichenfläche. */
  bg: string;
}

export interface SvgOptionen {
  /**
   * **Pflicht, nicht optional.** Der Auftrag erlaubte einen Standardwert, damit „die neun
   * Aufrufstellen nicht alle gleichzeitig geändert werden müssen" — **es sind zwei**, beide im
   * Test (gemessen, nicht angenommen). Der Grund für den Standardwert besteht also nicht, und
   * ohne ihn kann in dieser Datei **kein** Farbwert überleben. Das ist Kriterium 1 in seiner
   * strengsten Form: nicht „keiner außer dem Standard", sondern keiner.
   */
  farben: TreppenFarben;
  /** Zielbreite der Zeichenfläche in px (Höhe folgt dem Seitenverhältnis). Default 480. */
  breitePx?: number;
  /** Rand in px. Default 28. */
  randPx?: number;
  /** Stufen nummerieren. Default true. */
  nummern?: boolean;
  /** Titel/Beschriftung (z. B. „Gerade Treppe · 16 Steig."). */
  titel?: string;
}

interface Bbox { minX: number; minY: number; maxX: number; maxY: number }

function bboxVon(z: TreppenZeichnung): Bbox {
  const pts: SvgP[] = [...z.umriss, ...z.lauflinie, z.pfeilBis, ...z.stufenlinien.flat()];
  const xs = pts.map((p) => p.x);
  const ys = pts.map((p) => p.y);
  return { minX: Math.min(...xs), minY: Math.min(...ys), maxX: Math.max(...xs), maxY: Math.max(...ys) };
}

/**
 * Rendert die Treppen-Zeichnung als maßstäblichen SVG-String.
 * Weltkoordinaten (mm, Nord=+y) werden auf die px-Fläche skaliert und Y gespiegelt (SVG y zeigt nach
 * unten), sodass „oben" in der Zeichnung auch oben ist.
 */
export function treppeAlsSvg(z: TreppenZeichnung, opt: SvgOptionen): string {
  const F = opt.farben;
  const breitePx = opt.breitePx ?? 480;
  const rand = opt.randPx ?? 28;
  const nummern = opt.nummern ?? true;

  const bb = bboxVon(z);
  const wMm = Math.max(1, bb.maxX - bb.minX);
  const hMm = Math.max(1, bb.maxY - bb.minY);
  const zeichenBreite = breitePx - 2 * rand;
  const skala = zeichenBreite / wMm;
  const hPx = hMm * skala + 2 * rand;

  // Welt(mm) → px, Y gespiegelt.
  const X = (x: number): number => rand + (x - bb.minX) * skala;
  const Y = (y: number): number => rand + (bb.maxY - y) * skala;
  const r2 = (n: number): number => Math.round(n * 100) / 100;
  const pt = (p: SvgP): string => `${r2(X(p.x))},${r2(Y(p.y))}`;

  const teile: string[] = [];
  teile.push(`<rect x="0" y="0" width="${r2(breitePx)}" height="${r2(hPx)}" fill="${F.bg}" stroke="${F.rahmen}"/>`);

  // Umriss
  if (z.umriss.length >= 2) {
    teile.push(`<polygon points="${z.umriss.map(pt).join(' ')}" fill="none" stroke="${F.umriss}" stroke-width="1.5"/>`);
  }
  // Trittstufen
  for (const [a, b] of z.stufenlinien) {
    teile.push(`<line x1="${r2(X(a.x))}" y1="${r2(Y(a.y))}" x2="${r2(X(b.x))}" y2="${r2(Y(b.y))}" stroke="${F.stufe}" stroke-width="1"/>`);
  }
  // Stufennummern (Mitte jeder Trittstufen-Linie)
  if (nummern) {
    z.stufenlinien.forEach(([a, b], i) => {
      const mx = X((a.x + b.x) / 2);
      const my = Y((a.y + b.y) / 2);
      teile.push(`<text x="${r2(mx)}" y="${r2(my + 3)}" font-size="9" fill="${F.text}" text-anchor="middle">${i + 1}</text>`);
    });
  }
  // Lauflinie + Pfeilspitze
  if (z.lauflinie.length >= 2) {
    teile.push(`<polyline points="${z.lauflinie.map(pt).join(' ')}" fill="none" stroke="${F.lauflinie}" stroke-width="1.5"/>`);
    const spitze = z.lauflinie[z.lauflinie.length - 1];
    const vor = z.lauflinie[z.lauflinie.length - 2];
    const dx = X(spitze.x) - X(vor.x);
    const dy = Y(spitze.y) - Y(vor.y);
    const len = Math.hypot(dx, dy) || 1;
    const ux = dx / len;
    const uy = dy / len;
    const s = 9;
    const bx = X(z.pfeilBis.x);
    const by = Y(z.pfeilBis.y);
    const p1 = `${r2(bx - ux * s - uy * s * 0.5)},${r2(by - uy * s + ux * s * 0.5)}`;
    const p2 = `${r2(bx - ux * s + uy * s * 0.5)},${r2(by - uy * s - ux * s * 0.5)}`;
    teile.push(`<polygon points="${r2(bx)},${r2(by)} ${p1} ${p2}" fill="${F.lauflinie}"/>`);
  }
  // Titel + Gesamtmaß
  if (opt.titel) {
    teile.push(`<text x="${r2(rand)}" y="14" font-size="11" fill="${F.text}">${opt.titel}</text>`);
  }
  teile.push(`<text x="${r2(breitePx - rand)}" y="${r2(hPx - 8)}" font-size="9" fill="${F.text}" text-anchor="end">${Math.round(wMm)} × ${Math.round(hMm)} mm</text>`);

  return `<svg xmlns="http://www.w3.org/2000/svg" width="${r2(breitePx)}" height="${r2(hPx)}" viewBox="0 0 ${r2(breitePx)} ${r2(hPx)}">${teile.join('')}</svg>`;
}
