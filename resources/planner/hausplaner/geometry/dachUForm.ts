/**
 * Eingabeaufforderung 27: REINE, testbare Geometrie der GENEIGTEN U-Form (Hauptdach-Satteldach +
 * ZWEI spiegelsymmetrische Anbau-Satteldächer = „Flügel", außen bündig am Giebel, innen je eine Kehle).
 * SSOT: Test (dachUForm.test.ts) UND Engine (DachplanerProPage.buildCompoundPitchedU) nutzen DIESELBEN
 * Funktionen — der U-Bau zeichnet exakt das hier definierte Soll. Spiegelt die Engine-Konstanten aus
 * buildCompoundPitchedFaces. THREE-frei. KEINE Statik/Eindeckung/Produktwahl — nur Lage/Höhe/Länge.
 *
 * Koordinaten (Engine): x = Länge, z = Breite, y = oben. Hauptfirst entlang x bei z=0; main_N fällt
 * nach -z, main_S nach +z. normal == uDir×vDir mit normal.y = cos a > 0 (buildRoofFace stapelt nach außen).
 */

export interface Vec3 { x: number; y: number; z: number; }
export interface Punkt2D { x: number; y: number; }

export interface UFormEingabe {
  length: number; width: number;        // L, W
  widthB: number; lengthB: number;      // W_b (Anbau-Breite in x), L_b (Anbau-Länge in z)
  overhang: number; overhangGable: number; // oh, ohG
  pitchGrad: number;
  height: number; rafterHeight: number; // h, Sparrenhöhe [cm]
}

export interface UFlaeche {
  id: string; name: string;
  origin: Vec3; uDir: Vec3; vDir: Vec3; normal: Vec3;
  uMax: number; vMax: number;
  poly: Punkt2D[];
}
export interface UKehlsparren { name: string; start: Vec3; ende: Vec3; }

function endlich(n: unknown, fb = 0): number { return typeof n === 'number' && Number.isFinite(n) ? n : fb; }

/** Engine-Konstanten exakt wie buildCompoundPitchedFaces (DachplanerProPage.tsx ~Z.1155 ff.). */
export function uFormKonstanten(e: UFormEingabe) {
  const rad = endlich(e.pitchGrad) * Math.PI / 180;
  const L = Math.max(0.1, endlich(e.length)), W = Math.max(0.1, endlich(e.width));
  const W_b = Math.max(0.1, endlich(e.widthB)), L_b = Math.max(0.1, endlich(e.lengthB));
  const oh = endlich(e.overhang), ohG = endlich(e.overhangGable), rh = endlich(e.rafterHeight) / 100;
  const cos = Math.cos(rad), sin = Math.sin(rad), tan = Math.tan(rad);
  const kerve = rh * 0.25;
  const hPivot = endlich(e.height) + 0.14 + (rh / 2 - kerve) * cos;
  const yEaveEdge = hPivot - oh * tan;
  const hRafterCenter = (-rh / 2 + kerve) * cos;
  const slopeLen = (W / 2 + oh) / cos, vMaxMain = slopeLen;
  const slopeLenExt = (W_b / 2 + oh) / cos, vPeak = slopeLenExt;
  const uMaxMain = L + 2 * ohG;
  const uValleyBot = W_b / 2 + oh;
  const totalU = L_b + ohG + W_b / 2;
  const yRidge = hPivot + (slopeLen - oh / cos) * sin;
  const yRidgeExt = hPivot + (slopeLenExt - oh / cos) * sin;
  const cxWing = L / 2 - W_b / 2;     // Flügelmitten ±cxWing (außen bündig am Giebel)
  const apexZ = W / 2 - W_b / 2;      // Kehlspitze-z (gemeinsam für beide Flügel)
  return { rad, L, W, W_b, L_b, oh, ohG, cos, sin, tan, hPivot, yEaveEdge, hRafterCenter, vMaxMain, slopeLen, vPeak, slopeLenExt, uMaxMain, uValleyBot, totalU, yRidge, yRidgeExt, cxWing, apexZ };
}

/**
 * Soll-Polygon der genotchten main_S-Südfläche (lokale u/v; origin=(-L/2-ohG,yEaveEdge,W/2+oh),
 * uS=(1,0,0), vS=(0,sin,-cos)). ZWEI V-Notches (je Flügel), Außenseite am Giebel gekappt (wie L/T).
 */
export function mainSDoppelNotchPoly(e: UFormEingabe): Punkt2D[] {
  const k = uFormKonstanten(e);
  const half = k.W_b / 2 + k.oh;
  const wing = (cxWorld: number) => {
    const cxLocal = cxWorld + k.L / 2 + k.ohG;
    return { cxLocal, uL: cxLocal - half, uR: cxLocal + half };
  };
  const nL = wing(-k.cxWing), nR = wing(+k.cxWing);
  const poly: Punkt2D[] = [];
  poly.push({ x: 0, y: k.vMaxMain });
  if (nL.uL > 0) { poly.push({ x: 0, y: 0 }); poly.push({ x: nL.uL, y: 0 }); }
  else { const sV = k.vPeak / half; poly.push({ x: 0, y: Math.max(0, k.vPeak - sV * nL.cxLocal) }); }
  poly.push({ x: nL.cxLocal, y: k.vPeak });   // Kehlspitze links
  poly.push({ x: nL.uR, y: 0 });              // Innenfuß links
  poly.push({ x: nR.uL, y: 0 });              // Innenfuß rechts
  poly.push({ x: nR.cxLocal, y: k.vPeak });   // Kehlspitze rechts
  if (nR.uR < k.uMaxMain) { poly.push({ x: nR.uR, y: 0 }); poly.push({ x: k.uMaxMain, y: 0 }); }
  else { const sV = k.vPeak / (nR.cxLocal - nR.uR); poly.push({ x: k.uMaxMain, y: Math.max(0, k.vPeak + sV * (k.uMaxMain - nR.cxLocal)) }); }
  poly.push({ x: k.uMaxMain, y: k.vMaxMain });
  return poly;
}

/**
 * U-Bau nur aktiv, wenn die Geometrie gültig ist (sonst buildFlat-Fallback in der Engine). Prüft die
 * ROHEN Maße (nicht die geklemmten max(0.1,·)), sonst rutscht ein 0-/Leerfeld als gültig durch.
 */
export function uBauGueltig(e: UFormEingabe): boolean {
  const L = endlich(e.length), W = endlich(e.width), W_b = endlich(e.widthB), L_b = endlich(e.lengthB);
  const oh = endlich(e.overhang), ohG = endlich(e.overhangGable), pitch = endlich(e.pitchGrad);
  if (![L, W, W_b, L_b, oh, ohG, pitch].every((n) => Number.isFinite(n))) return false;
  if (!(L > 0 && W > 0 && W_b > 0 && L_b > 0)) return false;
  const rad = pitch * Math.PI / 180;
  if (!(rad > 0.5 * Math.PI / 180) || Math.cos(rad) < 1e-6) return false; // zu flach / Singularität
  if (W_b >= W) return false;                                            // Anbau ≥ Hauptdach
  const innenGap = L - 2 * W_b - 2 * oh;                                 // u_L_right − u_R_left
  return innenGap > 1e-6;                                                // Notches dürfen sich nicht schneiden
}

/** Die 6 Dachflächen der geneigten U-Form (main_N, main_S-Doppelnotch, je Flügel innen+außen). */
export function uFormFlaechen(e: UFormEingabe): UFlaeche[] {
  const k = uFormKonstanten(e);
  const { cos, sin, L, W, W_b, oh, ohG, yEaveEdge, vMaxMain, vPeak, uMaxMain, uValleyBot, totalU, slopeLenExt, cxWing } = k;
  const cx_R = +cxWing, cx_L = -cxWing;
  const rectN: Punkt2D[] = [{ x: 0, y: 0 }, { x: uMaxMain, y: 0 }, { x: uMaxMain, y: vMaxMain }, { x: 0, y: vMaxMain }];
  return [
    { id: 'main_N', name: 'Hauptdach Nord', origin: { x: L / 2 + ohG, y: yEaveEdge, z: -W / 2 - oh }, uDir: { x: -1, y: 0, z: 0 }, vDir: { x: 0, y: sin, z: cos }, normal: { x: 0, y: cos, z: -sin }, uMax: uMaxMain, vMax: vMaxMain, poly: rectN },
    { id: 'main_S', name: 'Hauptdach Süd', origin: { x: -L / 2 - ohG, y: yEaveEdge, z: W / 2 + oh }, uDir: { x: 1, y: 0, z: 0 }, vDir: { x: 0, y: sin, z: -cos }, normal: { x: 0, y: cos, z: sin }, uMax: uMaxMain, vMax: vMaxMain, poly: mainSDoppelNotchPoly(e) },
    // Rechter Flügel
    { id: 'ext_R_in', name: 'Anbau rechts innen', origin: { x: cx_R - W_b / 2 - oh, y: yEaveEdge, z: W / 2 - W_b / 2 }, uDir: { x: 0, y: 0, z: 1 }, vDir: { x: cos, y: sin, z: 0 }, normal: { x: -sin, y: cos, z: 0 }, uMax: totalU, vMax: slopeLenExt, poly: [{ x: 0, y: vPeak }, { x: uValleyBot, y: 0 }, { x: totalU, y: 0 }, { x: totalU, y: slopeLenExt }] },
    { id: 'ext_R_out', name: 'Anbau rechts außen', origin: { x: cx_R + W_b / 2 + oh, y: yEaveEdge, z: (W / 2 - W_b / 2) + totalU }, uDir: { x: 0, y: 0, z: -1 }, vDir: { x: -cos, y: sin, z: 0 }, normal: { x: sin, y: cos, z: 0 }, uMax: totalU, vMax: slopeLenExt, poly: [{ x: 0, y: 0 }, { x: totalU - uValleyBot, y: 0 }, { x: totalU, y: vPeak }, { x: 0, y: slopeLenExt }] },
    // Linker Flügel (handedness-korrekte x-Spiegelung)
    { id: 'ext_L_in', name: 'Anbau links innen', origin: { x: cx_L + W_b / 2 + oh, y: yEaveEdge, z: (W / 2 - W_b / 2) + totalU }, uDir: { x: 0, y: 0, z: -1 }, vDir: { x: -cos, y: sin, z: 0 }, normal: { x: sin, y: cos, z: 0 }, uMax: totalU, vMax: slopeLenExt, poly: [{ x: totalU, y: vPeak }, { x: totalU - uValleyBot, y: 0 }, { x: 0, y: 0 }, { x: 0, y: slopeLenExt }] },
    { id: 'ext_L_out', name: 'Anbau links außen', origin: { x: cx_L - W_b / 2 - oh, y: yEaveEdge, z: W / 2 - W_b / 2 }, uDir: { x: 0, y: 0, z: 1 }, vDir: { x: cos, y: sin, z: 0 }, normal: { x: -sin, y: cos, z: 0 }, uMax: totalU, vMax: slopeLenExt, poly: [{ x: 0, y: vPeak }, { x: uValleyBot, y: 0 }, { x: totalU, y: 0 }, { x: totalU, y: slopeLenExt }] },
  ];
}

/** Die zwei INNEN-Kehlsparren (zur Hofmitte), Mittellinie inkl. hRafterCenter (== Engine-Balken). */
export function uFormKehlsparren(e: UFormEingabe): UKehlsparren[] {
  const k = uFormKonstanten(e);
  const yMid = k.yEaveEdge + k.hRafterCenter;
  const yPeak = k.yEaveEdge + k.vPeak * k.sin + k.hRafterCenter;
  const cx_R = +k.cxWing, cx_L = -k.cxWing;
  return [
    { name: 'Kehlsparren rechts (Anbau)', start: { x: cx_R - k.W_b / 2 - k.oh, y: yMid, z: k.W / 2 + k.oh }, ende: { x: cx_R, y: yPeak, z: k.W / 2 - k.W_b / 2 } },
    { name: 'Kehlsparren links (Anbau)', start: { x: cx_L + k.W_b / 2 + k.oh, y: yMid, z: k.W / 2 + k.oh }, ende: { x: cx_L, y: yPeak, z: k.W / 2 - k.W_b / 2 } },
  ];
}
