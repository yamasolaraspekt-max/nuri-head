/**
 * W-3a — Dachaufbauten als 3D-Geometrie: DÜNNER Aufsatz auf die reine Engine (geometry/gaubeGeometrie).
 *
 * Baut je rechteckiger Dachfläche einen `SurfaceFrame` im three-Meter-Raum (aus DENSELBEN Welt-Ecken
 * wie dachMeshWelt, via weltZuThree — weltZuThree ist linear, gilt daher auch für Richtungsvektoren;
 * keine zweite Dach-Wahrheit) und ruft die Engine für Körper (Gauben/Kamin) + masshaltiges Loch
 * (fussabdruckUV). KEINE eigene Gauben-/Loch-Mathe — SSOT bleibt geometry/*. Reines TS (kein THREE),
 * damit im Node-Test lauffähig.
 */
import type { RoofAufbau } from '../../domain/scene.types';
import { weltZuThree } from './adapter';
import type { DachFlaeche } from './dachMesh';
import {
  pultGaubeGeometrie,
  giebelGaubeGeometrie,
  kaminGeometrie,
  aufbauBasis,
  weltAusLokal,
  surfacePointRein,
  fussabdruckUV,
  pruefeAufbau,
  type SurfaceFrame,
  type GaubeEingabe,
  type Vec3,
  type LokalPunkt,
} from '../../geometry/gaubeGeometrie';

/** Normal-Offset des Aufbau-Ursprungs über der Dachhaut (identisch zur Engine, kein Z-Fighting). */
const REF_OFF = 0.02;

function sub(a: Vec3, b: Vec3): Vec3 { return { x: a.x - b.x, y: a.y - b.y, z: a.z - b.z }; }
function cross(a: Vec3, b: Vec3): Vec3 {
  return { x: a.y * b.z - a.z * b.y, y: a.z * b.x - a.x * b.z, z: a.x * b.y - a.y * b.x };
}
function len(a: Vec3): number { return Math.hypot(a.x, a.y, a.z); }
function norm(a: Vec3): Vec3 { const l = len(a) || 1; return { x: a.x / l, y: a.y / l, z: a.z / l }; }

export interface AufbauFrame {
  frame: SurfaceFrame;
  neigungRad: number;
  /** Firsthöhe (three-Meter, y-up) — begrenzt die Gauben-Apexhöhe (kein Bauteil über First). */
  yRidgeThreeM: number;
  surfaceId: string;
}

/**
 * SurfaceFrame (three-Meter) einer rechteckigen Dachfläche aus ihren Welt-Ecken. origin = Traufe-links,
 * vRight = Richtung Traufe (u), vDown = Traufe→First (v, trotz Name „Down" bergauf), vNormal nach außen.
 */
export function flaecheZuFrame(f: DachFlaeche, yRidgeThreeM: number): AufbauFrame {
  const eaveL = weltZuThree(f.eaveLeft);
  const eaveR = weltZuThree(f.eaveRight);
  const ridgeL = weltZuThree(f.ridgeLeft);
  const vRightRaw = sub(eaveR, eaveL);
  const vDownRaw = sub(ridgeL, eaveL);
  const width = len(vRightRaw);
  const height = len(vDownRaw);
  const vRight = norm(vRightRaw);
  const vDown = norm(vDownRaw);
  let vNormal = norm(cross(vRight, vDown));
  if (vNormal.y < 0) vNormal = { x: -vNormal.x, y: -vNormal.y, z: -vNormal.z }; // nach außen/oben
  const frame: SurfaceFrame = { origin: eaveL, vRight, vDown, vNormal, width, height };
  return { frame, neigungRad: f.neigungRad, yRidgeThreeM, surfaceId: f.surfaceId };
}

export interface AufbauKoerper {
  /** Körper-Dreiecke in three-Metern (y-up), direkt für BufferGeometry. */
  tris: Array<[Vec3, Vec3, Vec3]>;
  /** Loch-Fußabdruck als (u,v)-Polygon (Meter) auf der Dachfläche; [] = kein sicheres Loch. */
  holePolyUV: Array<{ x: number; y: number }>;
  /** true = Aufbau ist nicht sicher darstellbar (Engine-Ampel „rot") → Prüf-Marker statt Körper. */
  pruefpflichtig: boolean;
  ampel: 'gruen' | 'gelb' | 'rot';
  /** Ankerpunkt (three) — für den Prüf-Marker bei pruefpflichtigen Aufbauten. */
  refWorld: Vec3;
}

function eingabe(a: RoofAufbau): GaubeEingabe {
  return {
    type: a.typ,
    x: a.x, y: a.y,
    width: a.breiteMm / 1000,
    height: a.hoeheMm / 1000,
    depth: a.tiefeMm / 1000,
    pitch: a.neigungGrad,
  };
}

/** Achsenparalleler Quader als 12 lokale Dreiecke (lx=Breite, ly=Höhe, lz=Tiefe/Falllinie). */
function boxTris(hw: number, hd: number, top: number, bot: number): LokalPunkt[][] {
  const c = (lx: number, ly: number, lz: number): LokalPunkt => ({ lx, ly, lz });
  const a = c(-hw, bot, -hd), b = c(hw, bot, -hd), cc = c(hw, top, -hd), d = c(-hw, top, -hd);
  const e = c(-hw, bot, hd), f = c(hw, bot, hd), g = c(hw, top, hd), h = c(-hw, top, hd);
  const q = (p1: LokalPunkt, p2: LokalPunkt, p3: LokalPunkt, p4: LokalPunkt): LokalPunkt[][] => [[p1, p2, p3], [p1, p3, p4]];
  return [
    ...q(a, b, cc, d),   // -z (hinten)
    ...q(f, e, h, g),    // +z (vorn)
    ...q(e, a, d, h),    // -x (links)
    ...q(b, f, g, cc),   // +x (rechts)
    ...q(d, cc, g, h),   // +y (oben)
    ...q(e, f, b, a),    // -y (unten)
  ];
}

/** Rechteck-Loch (u,v-Meter) für einfache Durchdringungen; [] wenn außerhalb der Fläche (Prüffeld). */
function rechteckLoch(uc: number, vc: number, breite: number, tiefe: number, W: number, H: number): Array<{ x: number; y: number }> {
  const m = 0.05;
  const u0 = uc - breite / 2, u1 = uc + breite / 2, v0 = vc - tiefe / 2, v1 = vc + tiefe / 2;
  if (u0 < m || u1 > W - m || v0 < m || v1 > H - m) return [];
  return [{ x: u0, y: v0 }, { x: u1, y: v0 }, { x: u1, y: v1 }, { x: u0, y: v1 }];
}

/**
 * Körper + Loch eines Aufbaus auf einer Dachfläche. Gauben/Kamin über die reine Engine; Dachfenster/
 * Lüfter/Sat/Lichtkuppel als flacher Aufsatz + Rechteckloch. Ungültige Lage (Ampel „rot" bzw. Loch
 * außerhalb) ⇒ pruefpflichtig=true (Aufrufer zeigt Marker), NIE ein Crash.
 */
export function aufbauKoerper(af: AufbauFrame, a: RoofAufbau): AufbauKoerper {
  const e = eingabe(a);
  const aRad = af.neigungRad;
  const ref = surfacePointRein(af.frame, e.x, e.y, REF_OFF);
  const basis = aufbauBasis(af.frame);
  const W = af.frame.width, H = af.frame.height;
  const lyApexMax = af.yRidgeThreeM - 1e-3 - ref.y;

  let lokalTris: LokalPunkt[][] = [];
  let holePolyUV: Array<{ x: number; y: number }> = [];
  let ampel: 'gruen' | 'gelb' | 'rot' = 'gruen';

  if (e.type === 'chimney') {
    const k = kaminGeometrie(e, aRad);
    lokalTris = boxTris(k.w / 2, k.d / 2, k.h, -k.sockel);
    holePolyUV = rechteckLoch(e.x * W, e.y * H, k.w, k.d, W, H);
    ampel = pruefeAufbau(af.frame, e, af.yRidgeThreeM).ampel;
  } else if (e.type === 'giebelgaube' || e.type === 'spitzgaube') {
    const g = giebelGaubeGeometrie(e, aRad, lyApexMax);
    lokalTris = [...g.koerperTris, ...g.dachTris];
    holePolyUV = fussabdruckUV(e, aRad, e.x * W, e.y * H, lyApexMax);
    ampel = pruefeAufbau(af.frame, e, af.yRidgeThreeM).ampel;
  } else if (e.type === 'schleppgaube' || e.type === 'flachgaube' || e.type === 'trapezgaube') {
    const g = pultGaubeGeometrie(e, aRad);
    lokalTris = [...g.koerperTris, ...g.dachTris];
    holePolyUV = fussabdruckUV(e, aRad, e.x * W, e.y * H);
    ampel = pruefeAufbau(af.frame, e, af.yRidgeThreeM).ampel;
  } else {
    // window | vent | sat | lichtkuppel — flacher Aufsatz + einfaches Rechteckloch.
    lokalTris = boxTris(e.width / 2, e.depth / 2, Math.max(0.08, e.height * 0.15), -0.05);
    holePolyUV = rechteckLoch(e.x * W, e.y * H, e.width, e.depth, W, H);
    ampel = holePolyUV.length ? 'gruen' : 'gelb';
  }

  const tris = lokalTris
    .filter((t) => t.length === 3)
    .map((t) => t.map((p) => weltAusLokal(ref, basis, p)) as [Vec3, Vec3, Vec3]);
  return { tris, holePolyUV, pruefpflichtig: ampel === 'rot', ampel, refWorld: ref };
}
