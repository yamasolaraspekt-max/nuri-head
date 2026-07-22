/**
 * Hausplaner — Treppentypen (Grundriss-Geometrie), wie kalk.pro: gerade, L (Viertelpodest),
 * U (Halbpodest), Spindel. Jeder Typ liefert eine typ-neutrale `TreppenZeichnung` (Umriss +
 * Trittstufen + Lauflinie + Pfeil), die `treppeAlsSvg` gemeinsam rendert. DIN-Stufung aus dem
 * getesteten `berechneTreppe`. Rein, keine three/Konva. (Gewendelte Wangen + Wendeltreppe folgen.)
 */

import { berechneTreppe, type TreppenBereich } from './treppenBerechnung';
import type { TreppenZeichnung, SvgP } from './treppeSvg';

export type TreppenTyp = 'gerade' | 'l-podest' | 'u-podest' | 'spindel';

export interface TreppenTypEingabe {
  typ: TreppenTyp;
  geschosshoehe: number;
  /** Laufbreite, mm. */
  laufbreite: number;
  gewuenschteSteigung?: number;
  bereich?: TreppenBereich;
  /** Nur Spindel: Außendurchmesser, mm (Default 1600). */
  durchmesser?: number;
  /** Nur Spindel: Gesamt-Drehwinkel in Grad (Default 270). */
  drehwinkelGrad?: number;
}

export interface TreppenTypErgebnis {
  typ: TreppenTyp;
  zeichnung: TreppenZeichnung;
  anzahlSteigungen: number;
  anzahlAuftritte: number;
  steigungshoehe: number;
  auftritt: number;
  bestanden: boolean;
  /** Grundfläche (Bounding-Box) der Treppe, mm. */
  grundflaeche: { breiteMm: number; tiefeMm: number };
}

const P = (x: number, y: number): SvgP => ({ x: Math.round(x), y: Math.round(y) });

function grundflaeche(z: TreppenZeichnung): { breiteMm: number; tiefeMm: number } {
  const pts = [...z.umriss, ...z.stufenlinien.flat(), ...z.lauflinie];
  const xs = pts.map((p) => p.x);
  const ys = pts.map((p) => p.y);
  return { breiteMm: Math.round(Math.max(...xs) - Math.min(...xs)), tiefeMm: Math.round(Math.max(...ys) - Math.min(...ys)) };
}

export function treppenTyp(e: TreppenTypEingabe): TreppenTypErgebnis {
  const t = berechneTreppe({
    geschosshoehe: e.geschosshoehe,
    gewuenschteSteigung: e.gewuenschteSteigung,
    laufbreite: e.laufbreite,
    bereich: e.bereich,
  });
  const A = t.anzahlAuftritte;
  const auf = t.auftritt;
  const w = e.laufbreite;

  let z: TreppenZeichnung;
  if (e.typ === 'gerade') z = geradeZeichnung(A, auf, w);
  else if (e.typ === 'l-podest') z = lPodestZeichnung(A, auf, w);
  else if (e.typ === 'u-podest') z = uPodestZeichnung(A, auf, w);
  else z = spindelZeichnung(A, e.durchmesser ?? 1600, e.drehwinkelGrad ?? 270);

  return {
    typ: e.typ,
    zeichnung: z,
    anzahlSteigungen: t.anzahlSteigungen,
    anzahlAuftritte: A,
    steigungshoehe: t.steigungshoehe,
    auftritt: auf,
    bestanden: t.bestanden,
    grundflaeche: grundflaeche(z),
  };
}

/** Gerade: ein Lauf entlang +x, Breite w in y. */
function geradeZeichnung(A: number, auf: number, w: number): TreppenZeichnung {
  const L = A * auf;
  const stufenlinien: Array<[SvgP, SvgP]> = [];
  for (let k = 1; k < A; k++) stufenlinien.push([P(k * auf, 0), P(k * auf, w)]);
  return {
    umriss: [P(0, 0), P(L, 0), P(L, w), P(0, w)],
    stufenlinien,
    lauflinie: [P(0, w / 2), P(L, w / 2)],
    pfeilBis: P(L, w / 2),
  };
}

/** L (Viertelpodest): Lauf +x, quadratisches Podest w×w, Lauf +y. Podest = eine Trittebene. */
function lPodestZeichnung(A: number, auf: number, w: number): TreppenZeichnung {
  const a1 = Math.ceil((A - 1) / 2);
  const a2 = A - 1 - a1;
  const L1 = a1 * auf;
  const L2 = a2 * auf;
  const stufenlinien: Array<[SvgP, SvgP]> = [];
  for (let k = 1; k < a1; k++) stufenlinien.push([P(k * auf, 0), P(k * auf, w)]);          // Lauf 1 (senkrechte Linien)
  for (let k = 1; k < a2; k++) stufenlinien.push([P(L1, w + k * auf), P(L1 + w, w + k * auf)]); // Lauf 2 (waagerechte Linien)
  // Podest-Grenzen
  stufenlinien.push([P(L1, 0), P(L1, w)]);        // Eintritt ins Podest
  stufenlinien.push([P(L1, w), P(L1 + w, w)]);    // Austritt aus dem Podest
  return {
    umriss: [P(0, 0), P(L1 + w, 0), P(L1 + w, w + L2), P(L1, w + L2), P(L1, w), P(0, w)],
    stufenlinien,
    lauflinie: [P(0, w / 2), P(L1 + w / 2, w / 2), P(L1 + w / 2, w + L2)],
    pfeilBis: P(L1 + w / 2, w + L2),
  };
}

/** U (Halbpodest): zwei parallele Läufe (links hoch, rechts hoch), Halbpodest oben. */
function uPodestZeichnung(A: number, auf: number, w: number): TreppenZeichnung {
  const a1 = Math.ceil((A - 1) / 2);
  const a2 = A - 1 - a1;
  const L1 = a1 * auf;
  const L2 = a2 * auf;
  const maxL = Math.max(L1, L2);
  const stufenlinien: Array<[SvgP, SvgP]> = [];
  for (let k = 1; k < a1; k++) stufenlinien.push([P(0, k * auf), P(w, k * auf)]);            // Lauf 1 links
  for (let k = 1; k < a2; k++) stufenlinien.push([P(w, maxL - k * auf), P(2 * w, maxL - k * auf)]); // Lauf 2 rechts
  stufenlinien.push([P(0, maxL), P(w, maxL)]);      // Eintritt Podest links
  stufenlinien.push([P(w, maxL), P(2 * w, maxL)]);  // Austritt Podest rechts
  return {
    umriss: [P(0, 0), P(2 * w, 0), P(2 * w, maxL + w), P(0, maxL + w)],
    stufenlinien,
    lauflinie: [P(w / 2, 0), P(w / 2, maxL + w / 2), P(3 * w / 2, maxL + w / 2), P(3 * w / 2, 0)],
    pfeilBis: P(3 * w / 2, 0),
  };
}

/** Spindel: Stufen als Keile um eine Mittelspindel; Außenradius = Durchmesser/2. */
function spindelZeichnung(A: number, durchmesser: number, drehwinkelGrad: number): TreppenZeichnung {
  const rOut = durchmesser / 2;
  const rIn = Math.max(60, rOut * 0.12); // Spindel/Auge
  const cx = rOut;
  const cy = rOut;
  const schritt = (drehwinkelGrad * Math.PI) / 180 / A;
  const auf = (a: number, r: number): SvgP => P(cx + r * Math.cos(a), cy + r * Math.sin(a));

  const stufenlinien: Array<[SvgP, SvgP]> = [];
  for (let k = 0; k <= A; k++) {
    const a = k * schritt;
    stufenlinien.push([auf(a, rIn), auf(a, rOut)]); // radiale Trittkanten
  }
  // Umriss: Außenbogen + Innenbogen zurück
  const umriss: SvgP[] = [];
  const seg = Math.max(2, A);
  for (let k = 0; k <= seg; k++) umriss.push(auf((k / seg) * A * schritt, rOut));
  for (let k = seg; k >= 0; k--) umriss.push(auf((k / seg) * A * schritt, rIn));
  // Lauflinie: Bogen auf mittlerem Radius
  const rMid = (rIn + rOut) / 2;
  const lauflinie: SvgP[] = [];
  for (let k = 0; k <= A; k++) lauflinie.push(auf(k * schritt, rMid));
  return { umriss, stufenlinien, lauflinie, pfeilBis: auf(A * schritt, rMid) };
}
