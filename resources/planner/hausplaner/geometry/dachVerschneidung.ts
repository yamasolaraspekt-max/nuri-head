/**
 * Eingabeaufforderung 26 (Teilstufe 1): REINE, testbare Geometrie der KEHL-/GRATLINIEN geneigter
 * L-/T-/U-Dachverschneidungen. Spiegelt EXAKT die Engine-Konstanten aus buildCompoundPitchedFaces
 * (SSOT) und dient als REGRESSIONSSCHLOSS: die bisher ungesicherte L/T-Compound-Geometrie wird damit
 * numerisch eingefroren (kein 3D-Render verfügbar). U-Form wird hier NUR als Soll-Vorschau berechnet,
 * NICHT in der Engine gebaut (bleibt Flachdach-Fallback).
 *
 * Geometrie (gleiche Neigung a beider Flächen, 90°-Grundrissecke): Kehle/Grat = 45°-Winkelhalbierende
 * in der Draufsicht; Kehlneigung β = atan(tan a / √2); 3D-Länge = (W_b/2+oh)·√(2+tan²a). Der schmälere
 * Anbaufirst trifft die Hauptdachfläche bei y = hPivot + (W_b/2)·tan a (= yRidgeExt < yRidge_main).
 *
 * Zwei Linien-Varianten je Verschneidung:
 *  - start/ende       = Sparren-Mittellinie (MIT hRafterCenter) -> Regressionsgleichheit zu den
 *    Engine-Balken (drawBeamBetweenPoints pStartL/pStartR -> pEndPeak).
 *  - startDeck/endeDeck = Dachhaut-/Deckungskehle (OHNE hRafterCenter) -> sichtbarer Overlay.
 *
 * Rein (KEIN three/React-Import). KEINE Statik/Schneelast/Eindeckung/Produktwahl — nur Lage/Höhe/Länge.
 */

export interface Vec3 { x: number; y: number; z: number; }

export interface VerschneidungEingabe {
  form: 'l' | 't' | 'u';
  length: number; width: number;        // L, W
  lengthB: number; widthB: number;      // L_b, W_b (Anbau)
  overhang: number; overhangGable: number; // oh, ohG
  pitchGrad: number;                    // gleiche Neigung a beider Flächen
  height: number; rafterHeight: number; // h, Sparrenhöhe [cm]
}

export type VerschneidungsArt = 'kehle' | 'grat';

export interface VerschneidungsLinie {
  art: VerschneidungsArt;
  seite: 'links' | 'rechts';
  start: Vec3; ende: Vec3;          // Sparren-Mittellinie (mit hRafterCenter) — == Engine-Balken
  startDeck: Vec3; endeDeck: Vec3;  // Dachhaut-/Deckungskehle (ohne hRafterCenter) — Overlay
  laenge3D: number;
  grundrissWinkelGrad: number;
  neigungGrad: number;
  pruefpflichtig: boolean;          // true statt falscher Linie bei entarteten/ungültigen Maßen
}

function endlich(n: unknown, fb = 0): number { return typeof n === 'number' && Number.isFinite(n) ? n : fb; }

/** Geschlossene Form (gleiche Neigung + gleicher Überstand): Kennwerte einer Kehle/eines Grats. */
export function kehlGratKennwerte(halbbreitePlusOh: number, aRad: number): { horizProjektion: number; neigungBeta: number; laenge3D: number } {
  const tanA = Math.tan(aRad);
  const hb = Math.max(0, endlich(halbbreitePlusOh));
  return {
    horizProjektion: hb * Math.SQRT2,
    neigungBeta: Math.atan(tanA / Math.SQRT2),
    laenge3D: hb * Math.sqrt(2 + tanA * tanA),
  };
}

/** Innere Helfer: spiegelt die Engine-Ableitungen (DachplanerProPage.buildCompoundPitchedFaces). */
function engineKonstanten(e: VerschneidungEingabe) {
  const rad = endlich(e.pitchGrad) * Math.PI / 180;
  const L = Math.max(0.1, endlich(e.length));
  const W = Math.max(0.1, endlich(e.width));
  const W_b = Math.max(0.1, endlich(e.widthB));
  const oh = endlich(e.overhang);
  const ohG = endlich(e.overhangGable);
  const rh = endlich(e.rafterHeight) / 100;
  const cosR = Math.cos(rad), sinR = Math.sin(rad), tanR = Math.tan(rad);
  const hPivot = endlich(e.height) + 0.14 + (rh / 2 - rh * 0.25) * cosR;
  const yEaveEdge = hPivot - oh * tanR;
  const hRafterCenter = (-rh / 2 + rh * 0.25) * cosR;
  const vPeak = (W_b / 2 + oh) / cosR;
  const yRidge = hPivot + ((W / 2 + oh) / cosR - oh / cosR) * sinR;
  const yRidgeExt = hPivot + ((W_b / 2 + oh) / cosR - oh / cosR) * sinR;
  const uMaxMain = L + 2 * ohG;
  return { rad, L, W, W_b, oh, ohG, cosR, sinR, tanR, hPivot, yEaveEdge, hRafterCenter, vPeak, yRidge, yRidgeExt, uMaxMain };
}

function machtPruefpflichtig(e: VerschneidungEingabe): boolean {
  const rad = endlich(e.pitchGrad) * Math.PI / 180;
  const W = Math.max(0.1, endlich(e.width));
  return !(rad > 0.5 * Math.PI / 180)        // zu flach (kein echter Grat/Kehle)
    || Math.cos(rad) < 1e-6                    // Singularität
    || endlich(e.widthB) <= 0                  // kein Anbau
    || endlich(e.widthB) >= W;                 // Anbau gleich/breiter als Hauptdach -> First-Kollision
}

/** Baut eine Verschneidungslinie aus Anbau-Mitte cx + Seite (links/rechts). */
function lineFuer(e: VerschneidungEingabe, k: ReturnType<typeof engineKonstanten>, cx: number, seite: 'links' | 'rechts', art: VerschneidungsArt): VerschneidungsLinie {
  const halb = k.W_b / 2 + k.oh;
  const vorzeichen = seite === 'links' ? -1 : +1;
  const start: Vec3 = { x: cx + vorzeichen * halb, y: k.yEaveEdge + k.hRafterCenter, z: k.W / 2 + k.oh };
  const ende: Vec3 = { x: cx, y: k.yEaveEdge + k.vPeak * k.sinR + k.hRafterCenter, z: k.W / 2 - k.W_b / 2 };
  const startDeck: Vec3 = { x: start.x, y: k.yEaveEdge, z: start.z };
  const endeDeck: Vec3 = { x: ende.x, y: k.yEaveEdge + k.vPeak * k.sinR, z: ende.z };
  const kw = kehlGratKennwerte(halb, k.rad);
  return {
    art, seite, start, ende, startDeck, endeDeck,
    laenge3D: kw.laenge3D,
    grundrissWinkelGrad: 45,
    neigungGrad: kw.neigungBeta * 180 / Math.PI,
    pruefpflichtig: false,
  };
}

/**
 * Liefert die Kehl-/Gratlinien einer geneigten L-/T-/U-Verschneidung. L/T spiegeln EXAKT die Engine;
 * U liefert nur die spiegelsymmetrischen Soll-Linien (nicht in der Engine gebaut). Bei entarteten Maßen
 * werden die Linien als pruefpflichtig=true zurückgegeben (oder leere Liste) — NIE NaN/Infinity.
 */
export function verschneidungslinien(e: VerschneidungEingabe): VerschneidungsLinie[] {
  const pruef = machtPruefpflichtig(e);
  const k = engineKonstanten(e);

  if (e.form === 'u') {
    // U = zwei spiegelsymmetrische Kehlen. Die Kehle liegt INNEN (hofseitig): linker Flügel -> innere
    // (+x) Flanke (seite 'rechts'), rechter Flügel -> innere (−x) Flanke (seite 'links') -> Kehl-START
    // bei |x| = cxWing − (W_b/2+oh), NICHT auf der Giebel-/Außenseite. (EA27: U geneigt wird gebaut.)
    const cxWing = k.L / 2 - k.W_b / 2;
    const links = lineFuer(e, k, -cxWing, 'rechts', 'kehle');
    const rechts = lineFuer(e, k, +cxWing, 'links', 'kehle');
    return [links, rechts].map((l) => ({ ...l, pruefpflichtig: pruef }));
  }

  const cx = e.form === 't' ? 0 : k.L / 2 - k.W_b / 2;
  const cxLocal = cx + k.L / 2 + k.ohG;
  const uL = cxLocal - (k.W_b / 2 + k.oh);
  const uR = cxLocal + (k.W_b / 2 + k.oh);

  const out: VerschneidungsLinie[] = [];
  // links: nur falls die Kehle innerhalb der Hauptfläche beginnt (Engine-Regel) -> immer Kehle.
  if (uL > 0) out.push({ ...lineFuer(e, k, cx, 'links', 'kehle'), pruefpflichtig: pruef });
  // rechts: T mit innenliegender Kante -> Kehle; sonst (L bündig) -> Grat.
  const rechtsArt: VerschneidungsArt = (e.form === 't' && uR < k.uMaxMain) ? 'kehle' : 'grat';
  out.push({ ...lineFuer(e, k, cx, 'rechts', rechtsArt), pruefpflichtig: pruef });
  return out;
}
