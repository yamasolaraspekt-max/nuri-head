/**
 * Eingabeaufforderung 17 (+ Workflow-Fachspezifikation 3D/Dachdecker/Zimmermann):
 * REINE, testbare Geometrie für STEHENDE Dachaufbauten (Pultgauben: Schlepp/Flach/Trapez,
 * Giebel-/Spitzgaube, Kamin) und ihren ANSCHLUSS an die geneigte Hauptdachfläche.
 *
 * WARUM: Der bisherige Box-Hack in updateObstacles ließ hinten eine vertikale Rückwand bis
 * lokal y=height+rise stehen -> bei 35° Dachneigung ragte sie ~0,36 m ÜBER den First und
 * ~1,3 m über die Hauptdachfläche (numerisch bestätigt: Welt-Y 8,336 > First 7,978). Eine
 * echte Pultgaube hat hinten KEINE Wand — ihr Pultdach endet auf der Hauptdachebene (Anschnitt).
 * Diese Util erzeugt die Geometrie als EXPLIZITE Vertices, deren Rück-/Anschlusskante exakt auf
 * der Hauptdachebene liegt, und prüft jeden Aufbau numerisch (kein Render verfügbar).
 *
 * LOKALES SYSTEM (= verticalSurfaceQuaternion, lotrecht):
 *   lx = horizontal parallel Traufe (Breite)
 *   ly = Welt-Hoch (der Aufbau steht lotrecht)
 *   lz = horizontale Falllinie: +lz = down-slope = Front/Traufe, -lz = up-slope = Richtung First
 *   Ursprung O = surfacePoint(surf, obs.x, obs.y, 0.02) (0,02 m über der Dachhaut, kein Z-Fighting)
 *
 * KERNFORMELN (allgemeingültig, da nMain·X=0, nMain·Y=cos a, nMain·Z=sin a):
 *   (1) signierter Außenabstand zur Hauptdachebene: s(P) = nMain·(P - oMain); Dachhaut s≈0, Referenz s≈0.02
 *   (2) Hauptdach-Linie durch O im Falllinien-Schnitt: ly_roof(lz) = -tan(a)*lz  (Locus s=0.02)
 *       -> Anschlusskante hinten (lz=-d/2): ly=+tan(a)*d/2 (hoch, auf Dach). Front unten (lz=+d/2): ly=-tan(a)*d/2.
 *
 * KOPPLUNG Pultneigung an Hauptdach: das Pultdach geht von der Front-Oberkante zur Anschlusskante
 *   auf dem Dach -> tan(b) = tan(a) - h/d. Machbarkeit: d*tan(a) > h (sonst Anschluss unmöglich);
 *   Entwässerung: b >= minNeigung -> h <= h_max = d*(tan a - tan(minNeigung)); sonst h klemmen.
 *
 * Rein (keine THREE-/React-Abhängigkeit, im Node-Test lauffähig). KEINE Dacheindeckung, KEINE Statik,
 * KEINE Schneelast — nur Lage/Höhe/Anschluss-Geometrie. Realer Tragwerksplaner/Dachdecker bleibt nötig.
 */

import { stehendeAufbauBasis, type Vec3 as BasisVec3 } from './aufbauOrientierung';

export interface Vec3 { x: number; y: number; z: number; }
/** Punkt im lokalen, lotrechten Aufbau-System (lx=Breite, ly=Welt-Hoch, lz=Falllinie). */
export interface LokalPunkt { lx: number; ly: number; lz: number; }
export type Dreieck = [LokalPunkt, LokalPunkt, LokalPunkt];
export type Linie = [LokalPunkt, LokalPunkt];

/** Reines Abbild des Engine-`surf`-Objekts (ohne THREE). */
export interface SurfaceFrame {
  origin: Vec3; vRight: Vec3; vDown: Vec3; vNormal: Vec3; width: number; height: number;
}

export interface GaubeEingabe {
  type: string;          // schleppgaube | flachgaube | trapezgaube | giebelgaube | spitzgaube | chimney
  x: number; y: number;  // rel. Position (0..1) auf der Fläche
  width: number; height: number; depth: number; // w, sichtbare Fronthöhe h, horizontale Tiefe d
  pitch?: number;        // advisorisch (Gaubenneigung wird i.d.R. abgeleitet)
}

// --- kleine reine Vektor-Helfer (keine THREE) -----------------------------------------------------
function endlich(n: unknown, fb = 0): number { return typeof n === 'number' && Number.isFinite(n) ? n : fb; }
function add(a: Vec3, b: Vec3): Vec3 { return { x: a.x + b.x, y: a.y + b.y, z: a.z + b.z }; }
function sub(a: Vec3, b: Vec3): Vec3 { return { x: a.x - b.x, y: a.y - b.y, z: a.z - b.z }; }
function scale(a: Vec3, s: number): Vec3 { return { x: a.x * s, y: a.y * s, z: a.z * s }; }
function dot(a: Vec3, b: Vec3): number { return a.x * b.x + a.y * b.y + a.z * b.z; }
function cross(a: Vec3, b: Vec3): Vec3 {
  return { x: a.y * b.z - a.z * b.y, y: a.z * b.x - a.x * b.z, z: a.x * b.y - a.y * b.x };
}
function laenge(a: Vec3): number { return Math.hypot(a.x, a.y, a.z); }
function norm(a: Vec3): Vec3 { const l = laenge(a) || 1; return scale(a, 1 / l); }

// --- Engine-Formeln rein gespiegelt ---------------------------------------------------------------
/** surfacePoint(surf,x,y,off) = origin + x*width*vRight + y*height*vDown + off*vNormal. */
export function surfacePointRein(f: SurfaceFrame, x: number, y: number, off = 0): Vec3 {
  let p = { ...f.origin };
  p = add(p, scale(f.vRight, x * f.width));
  p = add(p, scale(f.vDown, y * f.height));
  p = add(p, scale(f.vNormal, off));
  return p;
}

export interface AufbauBasisWelt { xAxis: Vec3; yAxis: Vec3; zAxis: Vec3; }
/** Lotrechte Welt-Basis (identisch zur Engine verticalSurfaceQuaternion). */
export function aufbauBasis(f: SurfaceFrame): AufbauBasisWelt {
  const b = stehendeAufbauBasis(f.vDown as BasisVec3);
  return { xAxis: b.xAxis as Vec3, yAxis: b.yAxis as Vec3, zAxis: b.zAxis as Vec3 };
}

/** Lokalen Punkt in Weltkoordinaten transformieren (genau wie grp.position + grp.quaternion). */
export function weltAusLokal(refWorld: Vec3, basis: AufbauBasisWelt, p: LokalPunkt): Vec3 {
  return add(add(add(refWorld, scale(basis.xAxis, p.lx)), scale(basis.yAxis, p.ly)), scale(basis.zAxis, p.lz));
}

/** Hauptdach-Ebene: Außennormale nMain und ein Punkt oMain (= surfacePoint(...,0)). */
export interface Hauptdach { nMain: Vec3; oMain: Vec3; yRidge: number; aRad: number; }
export function hauptdachAusFrame(f: SurfaceFrame, yRidge: number): Hauptdach {
  return { nMain: norm(f.vNormal), oMain: surfacePointRein(f, 0, 0, 0), yRidge, aRad: neigungAusFrame(f) };
}
/** Dachneigung a aus dem Frame (vDown steigt mit sin a in y). */
export function neigungAusFrame(f: SurfaceFrame): number {
  const vd = norm(f.vDown);
  return Math.asin(Math.max(-1, Math.min(1, vd.y)));
}
/** Signierter Außenabstand eines Weltpunkts zur Hauptdachebene (Dachhaut≈0, außen>0, eingebettet<0). */
export function signierterAbstand(P: Vec3, h: Hauptdach): number {
  return dot(h.nMain, sub(P, h.oMain));
}

// --- Pultgauben-Geometrie (Schlepp/Flach/Trapez) --------------------------------------------------
export const MIN_PULT_GRAD = 5;   // Schleppgaube: Mindest-Entwässerungsneigung
export const MIN_FLACH_GRAD = 2;  // Flachdachgaube: noch flacher zulässig
const REF_OFF = 0.02;             // Normal-Offset des Ursprungs O (s der Referenzebene)

export interface PultGaube {
  typ: string;
  feasible: boolean;
  angepasst: boolean;
  hinweis?: string;
  pultPitchRad: number;   // abgeleitete Pultneigung b
  h: number; d: number; w: number; // ggf. geklemmte Maße
  /** benannte lokale Eckpunkte. */
  verts: { fBL: LokalPunkt; fBR: LokalPunkt; fTL: LokalPunkt; fTR: LokalPunkt; bL: LokalPunkt; bR: LokalPunkt };
  /** Körper (Front + 2 Wangen) als Dreieckssuppe (Material wall). */
  koerperTris: Dreieck[];
  /** Pultdach als Dreieckssuppe (eigene Ebene gGaubenHaut). */
  dachTris: Dreieck[];
  /** Fenster in der Frontebene. */
  fenster: { center: LokalPunkt; breite: number; hoehe: number };
  /** Anschlusslinien auf der Hauptdachebene. */
  anschluss: { hinten: Linie; links: Linie; rechts: Linie; front: Linie };
}

/**
 * Konstruiert eine Pultgaube (Schlepp/Flach/Trapez) mit Anschlusskante EXAKT auf der Hauptdachebene.
 * Maße werden bei Bedarf geklemmt (Machbarkeit/Entwässerung) und das per `angepasst`/`feasible` gemeldet.
 */
export function pultGaubeGeometrie(e: GaubeEingabe, aRad: number): PultGaube {
  const typ = e.type;
  const a = endlich(aRad);
  const tanA = Math.tan(a);
  const w = Math.max(0.3, endlich(e.width));
  const d = Math.max(0.3, endlich(e.depth));
  const hWunsch = Math.max(0.2, endlich(e.height));
  const minGrad = typ === 'flachgaube' ? MIN_FLACH_GRAD : MIN_PULT_GRAD;
  const tanMin = Math.tan(minGrad * Math.PI / 180);

  let feasible = true;
  let angepasst = false;
  const hinweise: string[] = [];

  // Machbarkeit: d*tan a > h, sonst kann die Front nicht unter die Hauptdach-Rückkante einbinden.
  const hMax = d * (tanA - tanMin);
  let h = hWunsch;
  if (hMax <= 0.05) {
    // Dach zu flach für eine einbindende Pultgaube -> nicht sicher darstellbar.
    feasible = false;
    h = Math.max(0.2, Math.min(hWunsch, 0.3));
    hinweise.push('Hauptdach zu flach für einbindende Pultgaube — prüfpflichtig (schematisch).');
  } else if (h > hMax) {
    h = hMax; angepasst = true;
    hinweise.push('Fronthöhe an Hauptdachneigung/Tiefe gekoppelt (gekürzt), damit der Rückanschluss exakt aufgeht.');
  }
  // Abgeleitete Pultneigung b: Dach von Front-Oberkante zur Anschlusskante auf dem Dach.
  const tanB = Math.max(tanMin, tanA - h / d);
  const pultPitchRad = Math.atan(tanB);

  // Breiten: Trapezgaube vorne schmaler, hinten breiter; sonst konstant.
  const wFront = typ === 'trapezgaube' ? w * 0.6 : w;
  const wBack = w;
  const lyRoofFront = -tanA * (d / 2);  // ly der Front-Unterkante (auf Dach)
  const lyRoofBack = +tanA * (d / 2);   // ly der Anschlusskante hinten (auf Dach, höher)

  const fBL: LokalPunkt = { lx: -wFront / 2, ly: lyRoofFront, lz: +d / 2 };
  const fBR: LokalPunkt = { lx: +wFront / 2, ly: lyRoofFront, lz: +d / 2 };
  const fTL: LokalPunkt = { lx: -wFront / 2, ly: lyRoofFront + h, lz: +d / 2 };
  const fTR: LokalPunkt = { lx: +wFront / 2, ly: lyRoofFront + h, lz: +d / 2 };
  const bL: LokalPunkt = { lx: -wBack / 2, ly: lyRoofBack, lz: -d / 2 };
  const bR: LokalPunkt = { lx: +wBack / 2, ly: lyRoofBack, lz: -d / 2 };

  // Körper: Front-Quad (2 Dreiecke, Normale +lz) + 2 Wangen-Dreiecke. KEINE Rückwand, KEIN Boden.
  const koerperTris: Dreieck[] = [
    [fBL, fBR, fTR], [fBL, fTR, fTL],   // Front
    [fBL, fTL, bL],                      // Wange links
    [fBR, bR, fTR],                      // Wange rechts
  ];
  // Pultdach: Quad Front-Oberkante -> Anschlusskante (2 Dreiecke).
  const dachTris: Dreieck[] = [
    [fTL, fTR, bR], [fTL, bR, bL],
  ];
  const fenster = {
    center: { lx: 0, ly: lyRoofFront + 0.45 * h, lz: d / 2 + 0.01 },
    breite: wFront * 0.7, hoehe: h * 0.6,
  };
  const anschluss = {
    hinten: [bL, bR] as Linie,   // Rückanschluss (auf Dach)
    links: [fBL, bL] as Linie,   // linke Wangen-Unterkante (Rakelinie)
    rechts: [fBR, bR] as Linie,  // rechte Wangen-Unterkante (Rakelinie)
    front: [fBL, fBR] as Linie,  // Fußlinie/Front auf Dach
  };

  return {
    typ, feasible, angepasst,
    hinweis: hinweise.length ? hinweise.join(' ') : undefined,
    pultPitchRad, h, d, w,
    verts: { fBL, fBR, fTL, fTR, bL, bR },
    koerperTris, dachTris, fenster, anschluss,
  };
}

// --- Giebel-/Spitzgaube (schematisch, ehrlich „teilweise") ----------------------------------------
export interface GiebelGaube {
  typ: string;
  feasible: boolean;
  angepasst: boolean;
  hinweis?: string;
  apex: LokalPunkt;
  /** lokales lz, an dem der eigene Gaubenfirst die Hauptdachebene trifft (Rückende). */
  firstEndeLz: number;
  koerperTris: Dreieck[]; // Frontgiebel (lotrecht) + (E24) bei hWall>0 die zwei Wangen
  dachTris: Dreieck[];    // zwei Gaubendachflächen — E24: EBEN (planar), sonst Legacy (gewölbt)
  fenster: { center: LokalPunkt; breite: number; hoehe: number };
  anschluss: { kehleLinks: Linie; kehleRechts: Linie; front: Linie }; // Legacy/Schematik (Bestand, unverändert)
  // Eingabeaufforderung 24 (additiv): planare Dachflächen + echte Plane∩Plane-Kehlschnittlinien.
  dachPlanar: boolean;          // true = dachTris sind exakt eben (zwei echte Dachflächen)
  echteKehle: boolean;          // true = echteKehleLinks/Rechts sind aus Flächenverschneidung berechnet
  echteKehleLinks?: Linie;      // [kehleFrontL, firstEnde] (Dach∩Hauptdach) — nur wenn echteKehle
  echteKehleRechts?: Linie;     // [kehleFrontR, firstEnde]
  wangeTris: Dreieck[];         // lotrechte Seitenwangen (nur hWall>0), [] sonst — auch in koerperTris enthalten
  verts: {
    fBL: LokalPunkt; fBR: LokalPunkt; fWL: LokalPunkt; fWR: LokalPunkt;
    apex: LokalPunkt; firstEnde: LokalPunkt;
    kehleFrontL: LokalPunkt; kehleFrontR: LokalPunkt;   // Traufe∩Hauptdach (E24)
    kehleEndeL: LokalPunkt; kehleEndeR: LokalPunkt;     // Legacy (Firsthöhe)
  };
}

/**
 * Giebel-/Spitzgaube (schematisch, Status „teilweise"): lotrechte Frontgiebelwand, zwei
 * Gaubendachflächen, eigener horizontaler First, der UNTERHALB des Hauptfirsts auf der
 * Hauptdachebene ausläuft (zwei schematische Kehllinien). Spitzgaube = Sonderfall ohne
 * vertikale Frontwand (h_wall=0). `lyApexMax` (optional) begrenzt die lokale Apexhöhe, damit
 * die Spitze NICHT über den Hauptfirst ragt (vom Aufrufer aus yRidge - refWorld.y abgeleitet).
 */
export function giebelGaubeGeometrie(e: GaubeEingabe, aRad: number, lyApexMax = Infinity): GiebelGaube {
  const typ = e.type;
  const a = endlich(aRad);
  const tanA = Math.tan(a);
  const w = Math.max(0.3, endlich(e.width));
  const d = Math.max(0.3, endlich(e.depth));
  const hWunsch = Math.max(0.2, endlich(e.height));
  const halfW = w / 2;
  const pG = (endlich(e.pitch, 35) || 35) * Math.PI / 180;
  const lyRoofFront = -tanA * (d / 2);

  let hWall = typ === 'spitzgaube' ? 0 : hWunsch;
  let riseG = halfW * Math.tan(pG); // Höhe Traufe->First der kleinen Gaube
  let angepasst = false;
  const hinweise = ['Giebel-/Spitzgaube schematisch: Frontgiebel + zwei Anschluss-/Kehllinien. Echte Kehlverschneidung folgt später.'];

  // Apex unter den Hauptfirst klemmen (kein Bauteil über First): lyApex = lyRoofFront + hWall + riseG.
  let lyApex = lyRoofFront + hWall + riseG;
  if (lyApex > lyApexMax) {
    const ueber = lyApex - lyApexMax;
    // zuerst Giebelhöhe (rise), dann ggf. Wandhöhe reduzieren
    const reduceRise = Math.min(riseG - 0.1, ueber);
    riseG = Math.max(0.1, riseG - Math.max(0, reduceRise));
    lyApex = lyRoofFront + hWall + riseG;
    if (lyApex > lyApexMax) { hWall = Math.max(0, hWall - (lyApex - lyApexMax)); lyApex = lyRoofFront + hWall + riseG; }
    angepasst = true;
    hinweise.push('Gaubenhöhe gekürzt, damit die Spitze unter dem Hauptfirst bleibt.');
  }

  // Front-Eckpunkte (lotrechte Giebelwand bei lz=+d/2)
  const fBL: LokalPunkt = { lx: -halfW, ly: lyRoofFront, lz: +d / 2 };
  const fBR: LokalPunkt = { lx: +halfW, ly: lyRoofFront, lz: +d / 2 };
  const fWL: LokalPunkt = { lx: -halfW, ly: lyRoofFront + hWall, lz: +d / 2 };
  const fWR: LokalPunkt = { lx: +halfW, ly: lyRoofFront + hWall, lz: +d / 2 };
  const apexFront: LokalPunkt = { lx: 0, ly: lyApex, lz: +d / 2 };

  // Eigener First horizontal in -lz, bis er die Hauptdachebene trifft: ly_roof(lz)=lyApex -> lz=-lyApex/tanA.
  const firstEndeLz = tanA > 1e-6 ? Math.max(-d * 4, -lyApex / tanA) : -d / 2;
  const firstEnde: LokalPunkt = { lx: 0, ly: lyApex, lz: firstEndeLz };
  // Rückende-Kehlpunkte: lx=±halfW, lz=firstEndeLz, auf der Hauptdachebene (ly_roof = -tanA*lz = lyApex).
  const lyKehleEnde = -tanA * firstEndeLz;
  const kehleEndeL: LokalPunkt = { lx: -halfW, ly: lyKehleEnde, lz: firstEndeLz };
  const kehleEndeR: LokalPunkt = { lx: +halfW, ly: lyKehleEnde, lz: firstEndeLz };

  // Frontgiebel (lotrecht): Rechteck + Dreiecksspitze
  const koerperTris: Dreieck[] = [];
  if (hWall > 1e-6) {
    koerperTris.push([fBL, fBR, fWR], [fBL, fWR, fWL]); // Rechteck
    koerperTris.push([fWL, fWR, apexFront]);            // Giebeldreieck
  } else {
    koerperTris.push([fBL, fBR, apexFront]);            // reines Dreieck (Spitzgaube)
  }
  const fenster = {
    center: { lx: 0, ly: lyRoofFront + 0.45 * Math.max(0.3, hWall || riseG), lz: d / 2 + 0.01 },
    breite: w * 0.55, hoehe: Math.max(0.3, hWall || riseG) * 0.55,
  };
  const anschluss = {
    kehleLinks: [fBL, kehleEndeL] as Linie,   // Legacy schematische Kehl-/Anschlusslinie links (auf Dach)
    kehleRechts: [fBR, kehleEndeR] as Linie,  // rechts
    front: [fBL, fBR] as Linie,               // Fußlinie/Front auf Dach
  };

  // --- Eingabeaufforderung 24: PLANARE Dachflächen + ECHTE Plane∩Plane-Kehle --------------------
  // ly_eave (Traufhöhe der Gaubendachfläche) konsistent aus dem FINALEN (geklemmten) riseG ableiten.
  const lyEave = lyApex - riseG;                              // == lyRoofFront + hWall (final)
  const lzEave = tanA > 1e-6 ? -lyEave / tanA : (d / 2);      // Traufe∩Hauptdach (lz)
  const kehleFrontL: LokalPunkt = { lx: -halfW, ly: lyEave, lz: lzEave };
  const kehleFrontR: LokalPunkt = { lx: +halfW, ly: lyEave, lz: lzEave };

  // Richtung der echten Kehle = nMain × nL (zur Singularitätsprüfung).
  const nMain = { x: 0, y: Math.cos(a), z: Math.sin(a) };
  const nL = { x: -riseG, y: halfW, z: 0 };
  const kreuz = { x: nMain.y * nL.z - nMain.z * nL.y, y: nMain.z * nL.x - nMain.x * nL.z, z: nMain.x * nL.y - nMain.y * nL.x };
  const kreuzLen = Math.hypot(kreuz.x, kreuz.y, kreuz.z);
  const sehneLen = Math.hypot(kehleFrontL.lx - firstEnde.lx, kehleFrontL.ly - firstEnde.ly, kehleFrontL.lz - firstEnde.lz);
  const allesEndlich = [lyEave, lzEave, firstEndeLz, lyApex].every((n) => Number.isFinite(n));

  const echteKehle = tanA > 1e-6 && riseG > 0.1 && Math.tan(pG) > 1e-4
    && allesEndlich && sehneLen > 1e-3 && kreuzLen > 1e-6;

  let dachTris: Dreieck[];
  let dachPlanar: boolean;
  let echteKehleLinks: Linie | undefined;
  let echteKehleRechts: Linie | undefined;
  const wangeTris: Dreieck[] = [];

  if (echteKehle) {
    // Zwei EBENE Dachflächen: Front-Oberkante/Apex -> Traufe∩Dach (kehleFront) -> First∩Dach (firstEnde).
    dachTris = [
      [apexFront, fWL, kehleFrontL], [apexFront, kehleFrontL, firstEnde], // links (planar)
      [apexFront, firstEnde, kehleFrontR], [apexFront, kehleFrontR, fWR], // rechts (planar)
    ];
    dachPlanar = true;
    echteKehleLinks = [kehleFrontL, firstEnde];
    echteKehleRechts = [kehleFrontR, firstEnde];
    // Lotrechte Seitenwangen schließen den Spalt zwischen Frontfuß und Traufe∩Dach (nur hWall>0).
    if (hWall > 1e-6) {
      wangeTris.push([fBL, fWL, kehleFrontL], [fBR, kehleFrontR, fWR]);
      koerperTris.push(...wangeTris);
    }
    hinweise.length = 0;
    hinweise.push('Giebel-/Satteldachgaube mit ZWEI ebenen Dachflächen und ECHTEN Kehl-Schnittlinien (Dach∩Hauptdach). Maßhaltige Dachdeckerkehle/Verwahrung fachlich prüfen.');
  } else {
    // RÜCKFALL: bestehende (Legacy-)Gaubendachflächen (leicht gewölbter Quad) + schematische Linien.
    dachTris = [
      [apexFront, fWL, kehleEndeL], [apexFront, kehleEndeL, firstEnde],
      [apexFront, firstEnde, kehleEndeR], [apexFront, kehleEndeR, fWR],
    ];
    dachPlanar = false;
  }

  return {
    typ, feasible: true, angepasst,
    hinweis: hinweise.join(' '),
    apex: apexFront, firstEndeLz,
    koerperTris, dachTris, fenster, anschluss,
    dachPlanar, echteKehle, echteKehleLinks, echteKehleRechts, wangeTris,
    verts: { fBL, fBR, fWL, fWR, apex: apexFront, firstEnde, kehleFrontL, kehleFrontR, kehleEndeL, kehleEndeR },
  };
}

// --- Eingabeaufforderung 25: Gauben-Fußabdruck als (u,v)-Polygon auf der Dachfläche ---------------
/**
 * Liefert den realen Fußabdruck einer Gaube als (u,v)-Polygon (Meter) auf der Dachfläche — für das
 * echte Hauptdach-Loch (statt Rechteck). KONVERSION (alle Footprint-Verts liegen auf der Hauptdach-
 * ebene): u = uRef + lx ; v = vRef + ly·sin a − lz·cos a. SSOT: nutzt dieselben Generatoren wie die
 * gezeichnete Gaube (pult/giebelGaubeGeometrie), KEINE Parallel-Mathe.
 *   Pult (Schlepp/Flach/Trapez) -> 4-Eck [fBL,fBR,bR,bL] (Trapez via wFront/wBack automatisch).
 *   Giebel -> 5-Eck [fBL,fBR,kehleFrontR,firstEnde,kehleFrontL]; Spitz (hWall=0) -> 3-Eck nach Dedup.
 * Rückgabe [] = nicht polygonfähig (Rückfall auf Rechteck-Loch). CCW.
 */
export function fussabdruckUV(e: GaubeEingabe, aRad: number, uRef: number, vRef: number, lyApexMax = Infinity): { x: number; y: number }[] {
  const a = endlich(aRad);
  const sinA = Math.sin(a), cosA = Math.cos(a);
  const konv = (p: LokalPunkt) => ({ x: uRef + p.lx, y: vRef + p.ly * sinA - p.lz * cosA });
  if (e.type === 'schleppgaube' || e.type === 'flachgaube' || e.type === 'trapezgaube') {
    const g = pultGaubeGeometrie(e, a);
    return [g.verts.fBL, g.verts.fBR, g.verts.bR, g.verts.bL].map(konv);
  }
  if (e.type === 'giebelgaube' || e.type === 'spitzgaube') {
    const g = giebelGaubeGeometrie(e, a, lyApexMax);
    if (!g.echteKehle) return []; // Spitze nicht sicher auf der Dachlinie -> Rückfall
    const roh = [g.verts.fBL, g.verts.fBR, g.verts.kehleFrontR, g.verts.firstEnde, g.verts.kehleFrontL].map(konv);
    const out: { x: number; y: number }[] = []; // koinzidente Ecken entfernen (Spitzgaube hWall=0)
    for (const p of roh) if (!out.some((q) => Math.hypot(q.x - p.x, q.y - p.y) < 1e-4)) out.push(p);
    return out.length >= 3 ? out : [];
  }
  return [];
}

// --- Kamin ----------------------------------------------------------------------------------------
export interface KaminGeometrie { sockel: number; w: number; d: number; h: number; }
/** Kamin-Sockeltiefe an die Neigung koppeln: muss unter die tiefste (traufseitige) Footprint-Kante reichen. */
export function kaminGeometrie(e: GaubeEingabe, aRad: number): KaminGeometrie {
  const w = Math.max(0.2, endlich(e.width));
  const d = Math.max(0.2, endlich(e.depth));
  const h = Math.max(0.2, endlich(e.height));
  const sockel = Math.max(0.8, (d / 2) * Math.tan(endlich(aRad)) + 0.15);
  return { sockel, w, d, h };
}

// --- Numerische Prüfung (Ampel) -------------------------------------------------------------------
export type Ampel = 'gruen' | 'gelb' | 'rot';
export interface PruefBefund {
  ampel: Ampel;
  kriterien: { id: string; ok: boolean; ist: string; soll: string }[];
  kernbefund: string;
}

/**
 * Prüft einen STEHENDEN Aufbau gegen die physikalischen Bedingungen (kein Vertex über First,
 * Anschlusskante auf Hauptdach, Front lotrecht, Fuß eingebettet, Kamin spaltfrei).
 */
export function pruefeAufbau(f: SurfaceFrame, e: GaubeEingabe, yRidge: number): PruefBefund {
  const a = neigungAusFrame(f);
  const basis = aufbauBasis(f);
  const ref = surfacePointRein(f, e.x, e.y, REF_OFF);
  const hd = hauptdachAusFrame(f, yRidge);
  const tol = 1e-3, tolAnschluss = 2e-2;
  const krit: { id: string; ok: boolean; ist: string; soll: string }[] = [];

  const istKamin = e.type === 'chimney';
  const istGiebel = e.type === 'giebelgaube' || e.type === 'spitzgaube';

  let alleVerts: LokalPunkt[] = [];
  let anschlussPunkte: LokalPunkt[] = [];
  let frontOben: LokalPunkt | null = null, frontUnten: LokalPunkt | null = null;
  let geo: any = null;

  if (istKamin) {
    const k = kaminGeometrie(e, a);
    // Footprint-Ecken + Sockel-Unterkanten + Oberkante
    const hw = k.w / 2, hd2 = k.d / 2;
    const top = k.h, bot = -k.sockel;
    for (const lz of [-hd2, hd2]) for (const lx of [-hw, hw]) {
      alleVerts.push({ lx, ly: top, lz }, { lx, ly: bot, lz });
    }
  } else if (istGiebel) {
    const lyApexMax = yRidge - tol - ref.y; // Apex lokal so klemmen, dass Welt-Y < First
    geo = giebelGaubeGeometrie(e, a, lyApexMax);
    geo.koerperTris.concat(geo.dachTris).forEach((t: Dreieck) => alleVerts.push(...t));
    anschlussPunkte = [geo.anschluss.kehleLinks[1], geo.anschluss.kehleRechts[1]];
    alleVerts.push(geo.apex);
  } else {
    geo = pultGaubeGeometrie(e, a);
    geo.koerperTris.concat(geo.dachTris).forEach((t: Dreieck) => alleVerts.push(...t));
    anschlussPunkte = [geo.verts.bL, geo.verts.bR];
    frontUnten = geo.verts.fBL; frontOben = geo.verts.fTL;
  }

  const welt = alleVerts.map((p) => weltAusLokal(ref, basis, p));
  const maxY = welt.reduce((m, p) => Math.max(m, p.y), -Infinity);

  // AK1: kein Vertex über First
  krit.push({ id: 'AK1 kein Vertex über First', ok: maxY <= yRidge - tol,
    ist: `maxY=${maxY.toFixed(3)}`, soll: `≤ ${yRidge.toFixed(3)}` });

  // AK2/AK3: Anschluss-/Rückkante auf Hauptdachebene + keine Wand darüber
  if (anschlussPunkte.length) {
    const sWerte = anschlussPunkte.map((p) => signierterAbstand(weltAusLokal(ref, basis, p), hd));
    const okAnschluss = sWerte.every((s) => Math.abs(s - REF_OFF) <= tolAnschluss);
    krit.push({ id: 'AK2 Anschlusskante auf Hauptdach', ok: okAnschluss,
      ist: `s=${sWerte.map((s) => s.toFixed(3)).join(',')}`, soll: `|s-0.02|≤${tolAnschluss}` });
    // Keine Rückwand (nur Pultgauben): der höchste Punkt darf nicht als Wand über dem Dach stehen.
    // Bei der Giebelgaube ist der höchste Punkt die FRONT-Giebelspitze (lz=+d/2) — die darf vorne
    // legitim über das Dach ragen; dort greift AK1 (Apex < First).
    if (!istGiebel) {
      const hi = welt.reduce((m, p) => (p.y > m.y ? p : m), welt[0]);
      const sHi = signierterAbstand(hi, hd);
      krit.push({ id: 'AK3 keine Rückwand über Dach', ok: sHi <= REF_OFF + tolAnschluss,
        ist: `s(höchster)=${sHi.toFixed(3)}`, soll: `≤ ${(REF_OFF + tolAnschluss).toFixed(3)}` });
    }
  }

  // AK4: Front lotrecht (Pultgaube)
  if (frontOben && frontUnten) {
    const lotrecht = Math.abs(frontOben.lx - frontUnten.lx) < tol && Math.abs(frontOben.lz - frontUnten.lz) < tol
      && (frontOben.ly - frontUnten.ly) > 0;
    krit.push({ id: 'AK4 Front lotrecht', ok: lotrecht,
      ist: `Δlx=${(frontOben.lx - frontUnten.lx).toFixed(3)},Δlz=${(frontOben.lz - frontUnten.lz).toFixed(3)}`, soll: 'nur Δly>0' });
  }

  // AK5: Kamin spaltfrei (Sockel unter Hauptdach an up-slope-Ecke)
  if (istKamin) {
    const k = kaminGeometrie(e, a);
    const upBottom = weltAusLokal(ref, basis, { lx: 0, ly: -k.sockel, lz: -k.d / 2 });
    const sUp = signierterAbstand(upBottom, hd);
    krit.push({ id: 'AK5 Kaminsockel spaltfrei', ok: sUp <= -tol,
      ist: `s(Sockel up-slope)=${sUp.toFixed(3)}`, soll: '< 0 (unter Dach)' });
    krit.push({ id: 'AK1 Kamin unter/über First', ok: true, ist: 'lotrecht', soll: 'Achse ∥ (0,1,0)' });
  }

  const kritischOk = krit.filter((k) => /AK1|AK2|AK3|AK5/.test(k.id)).every((k) => k.ok);
  const allesOk = krit.every((k) => k.ok);
  const feasibleFlag = geo ? geo.feasible !== false : true;
  const ampel: Ampel = !kritischOk ? 'rot' : (!allesOk || !feasibleFlag) ? 'gelb' : 'gruen';
  const fail = krit.find((k) => !k.ok);
  const kernbefund = ampel === 'gruen'
    ? `${e.type}: Geometrie im Toleranzband, Anschluss auf Hauptdach, kein Vertex über First.`
    : `${e.type}: ${fail ? fail.id + ' verletzt (' + fail.ist + ', soll ' + fail.soll + ')' : 'prüfpflichtig'}.`;

  return { ampel, kriterien: krit, kernbefund };
}
