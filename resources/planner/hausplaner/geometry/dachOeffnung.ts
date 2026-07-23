/**
 * Eingabeaufforderung 15: reine, testbare Geometrie für DACHÖFFNUNGEN / Prüffelder von Aufbauten
 * (Gaube, Dachfenster, Kamin, Lichtkuppel). Liefert das Öffnungsrechteck inkl. Sicherheitsrand auf
 * der Dachfläche (in u/v-Relativ- und Meterkoordinaten) und einen Flag, ob es vollständig innerhalb
 * der (ggf. trapezförmigen) Dachfläche liegt.
 *
 * Damit kann der Planer die Öffnung als eigene Ebene „Dachöffnungen / Ausschnitte" sichtbar machen
 * (schematisch, gestrichelt, transparent) — OHNE riskante Polygon-Ausschnitt-Operation am Hauptdach.
 *
 * Maße/Koordinaten konsistent zu aufbauPlatzierung: u = parallel Traufe (rel. zu surf.width),
 * v = Traufe->First (rel. zu surf.height). Die Ausdehnung entlang der Schräge (v) ist art-abhängig:
 * Dachfenster -> hoeheM (Länge entlang Schräge), sonst -> tiefeM (Aufbauhöhe = Schrägen-Tiefe).
 *
 * Rein (keine React-/THREE-Abhängigkeit). KEINE Dacheindeckung/Material/Statik.
 */

import { verfuegbareBreiteM, type DachflaecheInfo } from './aufbauPlatzierung';

export interface OeffnungEingabe {
  art: string;
  surfaceId: string;
  xRel: number; yRel: number;
  breiteM: number; hoeheM: number; tiefeM: number;
  /** Eingabeaufforderung 25 (optional): Gaubenneigung + Apex-Klemmung für den maßhaltigen Fußabdruck. */
  pitch?: number;
  lyApexMax?: number;
}

export interface OeffnungRechteck {
  art: string;
  surfaceId: string;
  /** Prüffeld INKL. Sicherheitsrand, relativ (0..1) zur Dachflächen-Bounding-Box. */
  uMinRel: number; uMaxRel: number; vMinRel: number; vMaxRel: number;
  /** reine Öffnung (ohne Rand), Meter. */
  breiteM: number; tiefeM: number;
  sicherheitsrandM: number;
  /** true, wenn das Prüffeld vollständig innerhalb der (auch trapezförmigen) Dachfläche liegt. */
  innerhalb: boolean;
  /** löst die Öffnung eine Auswechslung der Sparren aus? (jede echte Dachöffnung tut das) */
  auswechslungErforderlich: boolean;
  hinweis: string;
}

function endlich(n: unknown, fb = 0): number {
  return typeof n === 'number' && Number.isFinite(n) ? n : fb;
}
function clamp(v: number, lo: number, hi: number): number {
  return Math.min(hi, Math.max(lo, v));
}

/** Schrägen-Tiefe (v-Ausdehnung) der Öffnung je Art: Dachfenster -> hoeheM, sonst -> tiefeM. */
export function oeffnungVTiefeM(o: { art: string; hoeheM: number; tiefeM: number }): number {
  return o.art === 'window' ? endlich(o.hoeheM) : endlich(o.tiefeM);
}

/**
 * Berechnet das Öffnungs-/Prüffeld-Rechteck eines Aufbaus auf der Dachfläche (inkl. Sicherheitsrand).
 * Niemals NaN/negativ; bei unbekannter/zu kleiner Fläche -> innerhalb=false (prüfpflichtig).
 */
export function oeffnungRechteck(o: OeffnungEingabe, f: DachflaecheInfo, sicherheitsrandM = 0.1): OeffnungRechteck {
  const W = Math.max(0.01, endlich(f?.breiteTraufeM));
  const H = Math.max(0.01, endlich(f?.hoeheM));
  const rand = Math.max(0, endlich(sicherheitsrandM, 0.1));
  const breiteM = Math.max(0.05, endlich(o.breiteM));
  const tiefeM = Math.max(0.05, oeffnungVTiefeM(o));
  const uM = clamp(endlich(o.xRel), 0, 1) * W;
  const vM = clamp(endlich(o.yRel), 0, 1) * H;
  const halbU = breiteM / 2 + rand;
  const halbV = tiefeM / 2 + rand;

  const uMinRel = clamp((uM - halbU) / W, 0, 1);
  const uMaxRel = clamp((uM + halbU) / W, 0, 1);
  const vMinRel = clamp((vM - halbV) / H, 0, 1);
  const vMaxRel = clamp((vM + halbV) / H, 0, 1);

  // innerhalb? Bounding-Box-Grenzen + (Trapez) lokal verfügbare Breite an der oberen/unteren Kante.
  const availUnten = verfuegbareBreiteM(f, vMinRel);
  const availOben = verfuegbareBreiteM(f, vMaxRel);
  const uCenter = W / 2;
  const halbAuf = breiteM / 2 + rand;
  const passtUnten = Math.abs(uM - uCenter) + halbAuf <= availUnten / 2 + 1e-6;
  const passtOben = Math.abs(uM - uCenter) + halbAuf <= availOben / 2 + 1e-6;
  const imBoundingBox = (uM - halbU) >= -1e-6 && (uM + halbU) <= W + 1e-6 && (vM - halbV) >= -1e-6 && (vM + halbV) <= H + 1e-6;
  const innerhalb = imBoundingBox && passtUnten && passtOben;

  return {
    art: o.art, surfaceId: o.surfaceId,
    uMinRel, uMaxRel, vMinRel, vMaxRel,
    breiteM, tiefeM, sicherheitsrandM: rand,
    innerhalb,
    auswechslungErforderlich: true,
    hinweis: innerhalb
      ? 'Schematische Öffnung/Prüffeld. Dachausschnitt, Auswechslung und Statik fachlich prüfen.'
      : 'Öffnung liegt teilweise außerhalb der Dachfläche — prüfpflichtig, Dachausschnitt fachlich prüfen.',
  };
}
