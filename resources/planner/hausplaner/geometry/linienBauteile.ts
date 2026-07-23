/**
 * Eingabeaufforderung 12: reine, testbare Logik für LINIENFÖRMIGE Dachbauteile (Schneefang als
 * erstes Bauteil; die Struktur ist bewusst generisch für Laufrost/Trittstufe/Wartungsgang/
 * Dachrinne/Firstlinie/Modul-Sperrlinie).
 *
 * Schneefang ist KEIN punktförmiger Aufbau, sondern eine Linie parallel zur Traufe im unteren
 * Dachbereich. Diese Datei erzeugt die GEOMETRIE der Linie flächenabhängig (Länge = verfügbare
 * Breite minus Randabstände; Trapez/Walm-aware über verfuegbareBreiteM aus Eingabeaufforderung 11)
 * und eine vorbereitete PV-Sperrzone (Abstand ober-/unterhalb). KEINE Statik/Schneelast, KEIN
 * Material/Produkt, KEINE Dacheindeckung.
 *
 * Koordinaten wie Aufbauten: u = parallel Traufe (rel. zu surf.width), v = Traufe->First
 * (rel. zu surf.height). Die Fläche ist horizontal um u = breiteTraufeM/2 zentriert.
 *
 * Rein (keine React-/THREE-Abhängigkeit).
 */

import { verfuegbareBreiteM, type DachflaecheInfo } from './aufbauPlatzierung';

export type LinienBauteilArt =
  | 'schneefang' | 'laufrost' | 'trittstufe' | 'wartungsgang'
  | 'dachrinne' | 'firstlinie' | 'modulsperrlinie';

/** Ein linienförmiges Dachbauteil (deckungsneutral; Geometrie + Sperrzonen-Vorbereitung). */
export interface DachLinienBauteil {
  art: LinienBauteilArt;
  surfaceId: string;
  /** rel. Lage in Dachflächenhöhe (v, 0=Traufe..1=First). */
  yRel: number;
  /** rel. Start in u-Richtung. */
  uStartRel: number;
  /** rel. Ende in u-Richtung. */
  uEndRel: number;
  /** geneigter Abstand zur Traufe, Meter. */
  abstandTraufeM: number;
  /** Linienlänge, Meter (Aufmaßwert). */
  laengeM: number;
  /** PV-Sperrabstand oberhalb der Linie, Meter. */
  sperrAbstandObenM: number;
  /** PV-Sperrabstand unterhalb der Linie, Meter. */
  sperrAbstandUntenM: number;
  pvSperrbereich: boolean;
  sichtbar: boolean;
  statischZuPruefen: boolean;
  deckungsneutral: boolean;
  hinweis: string;
}

export interface LinienBauteilErgebnis {
  bauteil: DachLinienBauteil | null;
  pruefpflichtig: boolean;
  hinweis: string;
}

export interface SchneefangOpts {
  /** Lage in Dachflächenhöhe (Richtwert, 10-20 % über Traufe). */
  yRel?: number;
  /** seitlicher Randanteil der Flächenbreite (Richtwert 5-10 %). */
  seitenRandAnteil?: number;
  sperrAbstandObenM?: number;
  sperrAbstandUntenM?: number;
}

export const SCHNEEFANG_HINWEIS =
  'Schneefang wird geometrisch als linienförmiges Dachbauteil erfasst. Auslegung, Anzahl, ' +
  'Befestigung und statische Prüfung müssen fachlich bzw. herstellerspezifisch erfolgen.';

function endlich(n: unknown, fb = 0): number {
  return typeof n === 'number' && Number.isFinite(n) ? n : fb;
}
function clamp(v: number, lo: number, hi: number): number {
  return Math.min(hi, Math.max(lo, v));
}
function round(n: number): number {
  return Math.round(n * 1000) / 1000;
}

/**
 * Setzt eine Schneefanglinie flächenabhängig in Traufnähe. Länge = lokal verfügbare Breite minus
 * seitliche Randabstände (Trapez/Walm: an der y-Position ausgewertet). Zu schmale/unbekannte
 * Fläche -> kein Bauteil, pruefpflichtig (Schneefang NICHT falsch setzen).
 */
export function platziereSchneefang(
  surfaceId: string,
  f: DachflaecheInfo,
  opts: SchneefangOpts = {},
): LinienBauteilErgebnis {
  const W = endlich(f?.breiteTraufeM);
  const H = endlich(f?.hoeheM);
  if (!surfaceId || W <= 0 || H <= 0) {
    return { bauteil: null, pruefpflichtig: true, hinweis: 'Dachfläche unbekannt/zu klein — Schneefang bitte prüfen.' };
  }
  const yRel = clamp(endlich(opts.yRel, 0.15), 0.05, 0.4); // 10-20 % über Traufe (Richtwert)
  const randAnteil = clamp(endlich(opts.seitenRandAnteil, 0.07), 0, 0.3);
  const availW = verfuegbareBreiteM(f, yRel);
  const seitlich = Math.max(0.2, randAnteil * W);
  const laengeM = availW - 2 * seitlich;
  if (laengeM <= 0.5) {
    return { bauteil: null, pruefpflichtig: true, hinweis: 'Dachfläche zu schmal für eine Schneefanglinie.' };
  }
  const uCenter = W / 2; // Fläche ist horizontal zentriert (auch beim Walm-Trapez)
  const uStartRel = clamp((uCenter - laengeM / 2) / W, 0, 1);
  const uEndRel = clamp((uCenter + laengeM / 2) / W, 0, 1);
  return {
    bauteil: {
      art: 'schneefang', surfaceId,
      yRel, uStartRel, uEndRel,
      abstandTraufeM: round(yRel * H),
      laengeM: round(laengeM),
      sperrAbstandObenM: Math.max(0, endlich(opts.sperrAbstandObenM, 0.3)),
      sperrAbstandUntenM: Math.max(0, endlich(opts.sperrAbstandUntenM, 0.1)),
      pvSperrbereich: true,
      sichtbar: true,
      statischZuPruefen: true,
      deckungsneutral: true,
      hinweis: SCHNEEFANG_HINWEIS,
    },
    pruefpflichtig: false,
    hinweis: '',
  };
}

/**
 * PV-Sperrzone eines Linienbauteils als relativer v-Bereich [vMinRel, vMaxRel] (für die spätere
 * PV-Belegung). Nutzt die Linien-Lage + die Sperrabstände ober-/unterhalb.
 */
export function sperrzoneVRel(b: DachLinienBauteil, hoeheM: number): { vMinRel: number; vMaxRel: number } | null {
  if (!b.pvSperrbereich) return null;
  const H = endlich(hoeheM);
  if (H <= 0) return null;
  const yM = clamp(endlich(b.yRel), 0, 1) * H;
  return {
    vMinRel: clamp((yM - endlich(b.sperrAbstandUntenM)) / H, 0, 1),
    vMaxRel: clamp((yM + endlich(b.sperrAbstandObenM)) / H, 0, 1),
  };
}

/** true, wenn eine rel. v-Position (z. B. eines PV-Moduls) in der Sperrzone eines Bauteils liegt. */
export function istInSperrzone(b: DachLinienBauteil, yRelModul: number, hoeheM: number): boolean {
  const z = sperrzoneVRel(b, hoeheM);
  if (!z) return false;
  const y = clamp(endlich(yRelModul), 0, 1);
  return y >= z.vMinRel && y <= z.vMaxRel;
}

/**
 * Leitet eine DachflaecheInfo aus echten Engine-Flächenwerten ab (für manuelles Setzen, wenn die
 * Fläche bereits gebaut ist): breiteTraufe = width, hoehe = height, breiteFirst aus dem Polygon
 * (u-Spanne an der Oberkante). Ohne Polygon -> rechteckige Annahme.
 */
export function flaecheInfoAusPolygon(
  surfaceId: string,
  width: number,
  height: number,
  polygon?: ReadonlyArray<{ x: number; y: number }>,
): DachflaecheInfo {
  const W = Math.max(0.1, endlich(width));
  const H = Math.max(0.1, endlich(height));
  if (!polygon || polygon.length < 3) {
    return { surfaceId, breiteTraufeM: W, breiteFirstM: W, hoeheM: H, form: 'rechteck' };
  }
  const vMax = Math.max(...polygon.map((p) => endlich(p.y)));
  const obenU = polygon.filter((p) => Math.abs(endlich(p.y) - vMax) < Math.max(0.01, vMax * 0.02)).map((p) => endlich(p.x));
  const breiteFirst = obenU.length >= 2 ? Math.max(...obenU) - Math.min(...obenU) : W;
  const form = breiteFirst <= 0.05 ? 'dreieck' : (breiteFirst < W * 0.95 ? 'trapez' : 'rechteck');
  return { surfaceId, breiteTraufeM: W, breiteFirstM: Math.max(0, breiteFirst), hoeheM: H, form };
}
