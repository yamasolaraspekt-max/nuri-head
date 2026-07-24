/**
 * D-c (Kern) — Dach-Mesh-Bauplan: REINE Funktion (kein three/WebGL) von RoofNode zu Welt-Dreiecken.
 * Grundlage: dach-andock-spec.md ▲D2 (reine Funktionen + dünner three-Aufsatz).
 *
 * Liefert die Dachflächen als Dreiecke in WELT-Koordinaten (mm, z-up, Nord=+y). Der three-Aufsatz
 * (szene.ts) rechnet je Eckpunkt via weltZuThree in Meter/y-up um und baut daraus BufferGeometry.
 * Testbar ohne Browser: die Dreiecks-Gesamtfläche MUSS der belastbaren dachFlaechen()-Summe entsprechen
 * (zwei unabhängige Rechnungen, gleiche Wahrheit). Überstand als Rechteck-Erweiterung modelliert (V1).
 */
import type { RoofNode } from '../../domain/scene.types';
import { istVerschneidungsForm, type RoofShape } from '../../domain/roofShape';
import { pruefeRechteckigeKontur } from '../../geometry/dachGeometrie';
import type { EngineRoofShape } from '../../geometry/dachformVorlagen';
// W-3b 2a-2: reine U-Form-Engine NUR aufgerufen (Byte-Treue). Liefert echte Flächen (poly in lokal u/v).
import { uFormFlaechen, uBauGueltig, type UFormEingabe } from '../../geometry/dachUForm';
// W-3b Teil 3: L/T-Verschneidungsflächen (byte-treuer Port) — gleiches (origin,uDir,vDir,poly)-Schema wie U.
import { verschneidungsFlaechen as ltFormFlaechen, lTBauGueltig, type VerschneidungEingabe } from '../../geometry/dachVerschneidung';

// W-3b (B1): Compile-Beweis, dass die Engine-Formen eine TEILMENGE der einen RoofShape-Wahrheit sind
// (kein gespiegelter Zweit-Typ, der auseinanderläuft). Bricht tsc, sobald jemand EngineRoofShape um
// einen Wert erweitert, den domain/roofShape.ts nicht kennt.
type _EngineFormenSindTeilmenge = EngineRoofShape extends RoofShape ? true : never;
const _engineSubsetBeweis: _EngineFormenSindTeilmenge = true;
void _engineSubsetBeweis;

export interface WeltPunkt3 {
  x: number;
  y: number;
  z: number;
}

export type Dreieck = [WeltPunkt3, WeltPunkt3, WeltPunkt3];

export interface DachMesh {
  dreiecke: Dreieck[];
  firstHoeheMm: number;
}

/** Eine Rohfläche (Welt-mm-Ecken) aus der geteilten Herleitung; 4 = Quad, 3 = Walm-Dreieck. */
interface RohFlaeche {
  surfaceId: string;
  rechteckig: boolean;
  neigungRad: number;
  /** 4 Ecken = [eaveLeft, eaveRight, ridgeRight, ridgeLeft]; 3 Ecken = Walm-Giebeldreieck. */
  ecken: WeltPunkt3[];
}

interface DachRoh {
  firstHoeheMm: number;
  flaechen: RohFlaeche[];
}

/** Standard-Sparrenhöhe (cm) — Konstruktions-Konstante, nicht im Modell; wirkt nur cm-weise auf Höhen. */
const SPARREN_HOEHE_CM = 20;

/**
 * Bounding-Box eines Polygons + deren Zentrum. Das Grundriss-Zentrum (Bbox-Mitte) ist der korrekte
 * Platzierungs-Anker der Verschneidungsformen (u UND l/t) — NICHT der Schwerpunkt: der Schwerpunkt eines
 * U/L/T-Polygons ist zur Masse hin verschoben (die Kerbe fehlt), was das Dach gegen den Wand-Footprint
 * versetzt (Evaluator-Befund U-Optik, gilt gleich für L/T). Die Bbox-Mitte deckt sich mit der Rechteck-Basis.
 */
function polygonBbox(poly: ReadonlyArray<{ x: number; y: number }>): { xMin: number; xMax: number; yMin: number; yMax: number; cx: number; cy: number } {
  let xMin = Infinity, xMax = -Infinity, yMin = Infinity, yMax = -Infinity;
  for (const p of poly) {
    if (p.x < xMin) xMin = p.x; if (p.x > xMax) xMax = p.x;
    if (p.y < yMin) yMin = p.y; if (p.y > yMax) yMax = p.y;
  }
  return { xMin, xMax, yMin, yMax, cx: (xMin + xMax) / 2, cy: (yMin + yMax) / 2 };
}

/**
 * W-3b 2a-2: EINE Abbildung `node.anbau` → `UFormEingabe` (Meter). U braucht ALLE vier Schenkelmaße
 * (uFormFlaechen nutzt widthB/lengthB) — fehlen sie, gibt es KEINE Eingabe (→ leer/Marker, kein
 * erfundener Wert). overhang/height/pitch stammen aus dem RoofNode; rafterHeight ist Standard-Konstante.
 */
function anbauZuEingabe(roof: RoofNode): UFormEingabe | null {
  const a = roof.anbau;
  if (!a || !(a.length > 0) || !(a.width > 0) || !(a.lengthB && a.lengthB > 0) || !(a.widthB && a.widthB > 0)) {
    return null;
  }
  return {
    length: a.length / 1000,
    width: a.width / 1000,
    lengthB: a.lengthB / 1000,
    widthB: a.widthB / 1000,
    overhang: roof.ueberstandMm / 1000,
    overhangGable: roof.ueberstandMm / 1000,
    pitchGrad: roof.neigungGrad,
    height: roof.traufhoeheMm / 1000,
    rafterHeight: SPARREN_HOEHE_CM,
  };
}

/**
 * Ohren-Schnitt-Triangulierung eines EINFACHEN Polygons (auch konkav — die U-Südfläche ist genotcht).
 * Liefert Index-Dreiecke in die Eingabe. Rein/testbar; entartete Polygone brechen sicher ab (kein Hang).
 */
function triangulierePolygon(poly: ReadonlyArray<{ x: number; y: number }>): Array<[number, number, number]> {
  const n = poly.length;
  if (n < 3) return [];
  const kreuz = (o: { x: number; y: number }, a: { x: number; y: number }, b: { x: number; y: number }): number =>
    (a.x - o.x) * (b.y - o.y) - (a.y - o.y) * (b.x - o.x);
  let flaeche = 0;
  for (let i = 0; i < n; i++) { const a = poly[i], b = poly[(i + 1) % n]; flaeche += a.x * b.y - b.x * a.y; }
  const idx = [...Array(n).keys()];
  if (flaeche < 0) idx.reverse(); // CCW erzwingen
  const imDreieck = (p: { x: number; y: number }, a: { x: number; y: number }, b: { x: number; y: number }, c: { x: number; y: number }): boolean => {
    const d1 = kreuz(a, b, p), d2 = kreuz(b, c, p), d3 = kreuz(c, a, p);
    return !(((d1 < 0) || (d2 < 0) || (d3 < 0)) && ((d1 > 0) || (d2 > 0) || (d3 > 0)));
  };
  const tris: Array<[number, number, number]> = [];
  let wache = 0;
  while (idx.length > 3 && wache++ < 2000) {
    let ohr = false;
    for (let i = 0; i < idx.length; i++) {
      const ia = idx[(i + idx.length - 1) % idx.length], ib = idx[i], ic = idx[(i + 1) % idx.length];
      const a = poly[ia], b = poly[ib], c = poly[ic];
      if (kreuz(a, b, c) <= 0) continue; // reflex/kollinear — kein Ohr
      let frei = true;
      for (const j of idx) { if (j === ia || j === ib || j === ic) continue; if (imDreieck(poly[j], a, b, c)) { frei = false; break; } }
      if (!frei) continue;
      tris.push([ia, ib, ic]);
      idx.splice(i, 1);
      ohr = true;
      break;
    }
    if (!ohr) break; // Sicherheitsnetz gegen entartete Polygone
  }
  if (idx.length === 3) tris.push([idx[0], idx[1], idx[2]]);
  return tris;
}

/**
 * W-3b 2a-2 + Teil 3: echte Dachflächen der Verschneidungsformen (in `dachRoh`, ersetzt den `→ []`-Guard).
 *  - u-shape: die 6 Flächen aus `uFormFlaechen` (Byte-Treue), Engine-Meter (x=Länge, z=Breite, y=oben).
 *  - l/t-shape (Teil 3): die 4 Flächen aus `verschneidungsFlaechen` (byte-treu `buildCompoundPitchedFaces`)
 *    — gleiches (origin,uDir,vDir,poly)-Schema, deshalb DIESELBE Transform/Triangulierung wie U.
 *  - Alle → Welt (mm, Nord=+y, um den Polygon-Schwerpunkt) transformiert und (konkav-sicher) trianguliert.
 *  - fehlendes/degeneriertes `anbau` ⇒ leer (Aufrufer setzt Prüf-Marker). Kein erfundener Wert, kein Crash.
 */
function verschneidungsFlaechen(roof: RoofNode): DachRoh {
  const leer: DachRoh = { firstHoeheMm: Math.round(roof.traufhoeheMm), flaechen: [] };
  const e = anbauZuEingabe(roof);
  if (!e) return leer;                            // fehlendes anbau → Marker/leer

  // Flächenquelle je Form; beide liefern dasselbe Flächen-Schema ⇒ eine Mapping-Schleife (eine Wahrheit).
  type FlaechenQuelle = ReadonlyArray<{ origin: WeltPunkt3; uDir: WeltPunkt3; vDir: WeltPunkt3; poly: Array<{ x: number; y: number }> }>;
  let quelle: FlaechenQuelle;
  if (roof.roofType === 'u-shape') {
    if (!uBauGueltig(e)) return leer;             // degeneriert → leer
    quelle = uFormFlaechen(e);
  } else {
    const ve: VerschneidungEingabe = { ...e, form: roof.roofType === 't-shape' ? 't' : 'l' };
    if (!lTBauGueltig(ve)) return leer;           // degeneriert → leer
    quelle = ltFormFlaechen(ve);
  }

  const azRad = (roof.firstAzimutGrad * Math.PI) / 180; // NICHT `rad` — kein zweiter Rechenweg der Rechteckbasis
  const rx = Math.sin(azRad), ry = Math.cos(azRad);     // Firstrichtung (Nord=+y)
  const qx = Math.cos(azRad), qy = -Math.sin(azRad);    // quer
  const M = 1000;
  const aRad = (roof.neigungGrad * Math.PI) / 180;

  // (u,v)→Engine-Grundriss (ex,ez) — reine Ableitung, für die Footprint-Zentrierung (gemeinsam u+l/t).
  const grund = (f: (typeof quelle)[number], uv: { x: number; y: number }): { ex: number; ez: number } => ({
    ex: f.origin.x + uv.x * f.uDir.x + uv.y * f.vDir.x,
    ez: f.origin.z + uv.x * f.uDir.z + uv.y * f.vDir.z,
  });
  // Engine-Footprint-Bbox über ALLE `quelle`-Flächen → dessen ZENTRUM auf das Grundriss-Zentrum (Bbox-
  // Mitte) der Wände legen. Quellen-agnostisch ⇒ EIN Anker platziert u UND l/t (SSOT, behebt die
  // Schwerpunkt-Fehlplatzierung: Traufe/Kehle sitzen auf der Kontur, kein Versatz gegen den Wand-Footprint).
  let exMin = Infinity, exMax = -Infinity, ezMin = Infinity, ezMax = -Infinity;
  for (const f of quelle) for (const uv of f.poly) {
    const g = grund(f, uv);
    if (g.ex < exMin) exMin = g.ex; if (g.ex > exMax) exMax = g.ex;
    if (g.ez < ezMin) ezMin = g.ez; if (g.ez > ezMax) ezMax = g.ez;
  }
  const engCx = (exMin + exMax) / 2, engCz = (ezMin + ezMax) / 2;
  const ziel = polygonBbox(roof.polygon);

  const flaechen: RohFlaeche[] = [];
  let maxZ = roof.traufhoeheMm;
  let nr = 0;
  for (const f of quelle) {
    for (const [i, j, k] of triangulierePolygon(f.poly)) {
      const ecken = [f.poly[i], f.poly[j], f.poly[k]].map((uv): WeltPunkt3 => {
        // lokal (u,v) → Engine-Welt → Ziel-Welt (mm). Anker: Engine-Footprint-Zentrum → Grundriss-Bbox-Mitte.
        const ex = f.origin.x + uv.x * f.uDir.x + uv.y * f.vDir.x;
        const ey = f.origin.y + uv.x * f.uDir.y + uv.y * f.vDir.y;
        const ez = f.origin.z + uv.x * f.uDir.z + uv.y * f.vDir.z;
        const dex = ex - engCx, dez = ez - engCz;
        const w: WeltPunkt3 = { x: ziel.cx + (dex * rx + dez * qx) * M, y: ziel.cy + (dex * ry + dez * qy) * M, z: ey * M };
        if (w.z > maxZ) maxZ = w.z;
        return w;
      });
      flaechen.push({ surfaceId: `${roof.id}#u${nr++}`, rechteckig: false, neigungRad: aRad, ecken });
    }
  }
  return { firstHoeheMm: Math.round(maxZ), flaechen };
}

/**
 * W-3a-fix (M1/SSOT): DIE EINE Herleitung von Basis ((u,v)→Welt) + Flächen-Ecken je roofType.
 * `dachMeshWelt` (Dreiecke) UND `dachflaechen` (Aufbau-Trägerflächen) lesen aus dieser Quelle — kein
 * zweiter Rechenweg mehr, der still divergieren kann. Rechteckige Formen (flach/pult/sattel) → Quads
 * (rechteckig=true); walm → 2 Trapez- + 2 Dreiecksflächen (rechteckig=false). KEIN mm-Runden der Ecken
 * (abgeleitete Render-Geometrie). firstHoeheMm = oberste Kante über ±0.
 *
 * @throws DachGeometrieUngueltig  bei nicht-rechteckiger Kontur (Kante 1).
 */
function dachRoh(roof: RoofNode): DachRoh {
  // W-3b 2a-2: Verschneidungsformen (l/t/u) laufen NICHT durch die pauschale Rechteck-Prüfung, sondern
  // über die reinen Engines (u = echte Flächen aus dachUForm; l/t noch leer, s. verschneidungsFlaechen).
  // Bleibt EINMAL hier in der geteilten Quelle ⇒ wirkt für dachMeshWelt (Triangulierung) UND dachflaechen.
  if (istVerschneidungsForm(roof.roofType)) {
    return verschneidungsFlaechen(roof);
  }
  const { laengeMm, spannMm, cx, cy } = pruefeRechteckigeKontur(roof.polygon, roof.firstAzimutGrad);
  const rad = (roof.firstAzimutGrad * Math.PI) / 180;
  const ux = Math.sin(rad);
  const uy = Math.cos(rad); // Firstrichtung (Nord=+y)
  const vx = Math.cos(rad);
  const vy = -Math.sin(rad); // quer

  const ue = roof.ueberstandMm;
  const a = (laengeMm + 2 * ue) / 2; // halbe Länge entlang First
  const b = (spannMm + 2 * ue) / 2; // halbe Spannweite quer
  const zt = roof.traufhoeheMm;
  const tan = Math.tan((roof.neigungGrad * Math.PI) / 180);
  const aRad = (roof.neigungGrad * Math.PI) / 180;

  const w = (u: number, v: number, z: number): WeltPunkt3 => ({ x: cx + u * ux + v * vx, y: cy + u * uy + v * vy, z });

  const flaechen: RohFlaeche[] = [];
  let firstHoehe = zt;

  switch (roof.roofType) {
    // W-3b Stufe 2a: 'rect' = rechteckige Grundform ⇒ definiertes Verhalten wie 'flach' (flache Fläche),
    // KEIN stiller Wegfall mehr durchs switch (Evaluator-Auflage aus Stufe 1).
    case 'flach':
    case 'rect':
      flaechen.push({
        surfaceId: `${roof.id}#0`, rechteckig: true, neigungRad: 0,
        ecken: [w(-a, -b, zt), w(a, -b, zt), w(a, b, zt), w(-a, b, zt)],
      });
      break;
    case 'pult': {
      const hoch = zt + 2 * b * tan; // Anstieg über die volle Spannweite
      firstHoehe = hoch;
      flaechen.push({
        surfaceId: `${roof.id}#0`, rechteckig: true, neigungRad: aRad,
        ecken: [w(-a, -b, zt), w(a, -b, zt), w(a, b, hoch), w(-a, b, hoch)],
      });
      break;
    }
    case 'sattel': {
      const first = zt + b * tan;
      firstHoehe = first;
      // Südseite (v=-b) und Nordseite (v=+b), First bei v=0.
      flaechen.push(
        { surfaceId: `${roof.id}#0`, rechteckig: true, neigungRad: aRad,
          ecken: [w(-a, -b, zt), w(a, -b, zt), w(a, 0, first), w(-a, 0, first)] },
        { surfaceId: `${roof.id}#1`, rechteckig: true, neigungRad: aRad,
          ecken: [w(a, b, zt), w(-a, b, zt), w(-a, 0, first), w(a, 0, first)] },
      );
      break;
    }
    case 'walm': {
      const first = zt + b * tan;
      firstHoehe = first;
      const rr = Math.max(0, a - b); // halbe Firstlänge = (L−B)/2 (Überstand kürzt sich)
      flaechen.push(
        // 2 Haupt-(Trapez-)Flächen …
        { surfaceId: `${roof.id}#0`, rechteckig: false, neigungRad: aRad,
          ecken: [w(-a, -b, zt), w(a, -b, zt), w(rr, 0, first), w(-rr, 0, first)] },
        { surfaceId: `${roof.id}#1`, rechteckig: false, neigungRad: aRad,
          ecken: [w(a, b, zt), w(-a, b, zt), w(-rr, 0, first), w(rr, 0, first)] },
        // … 2 Walm-(Dreiecks-)Flächen an den Giebelenden.
        { surfaceId: `${roof.id}#2`, rechteckig: false, neigungRad: aRad,
          ecken: [w(a, -b, zt), w(a, b, zt), w(rr, 0, first)] },
        { surfaceId: `${roof.id}#3`, rechteckig: false, neigungRad: aRad,
          ecken: [w(-a, b, zt), w(-a, -b, zt), w(-rr, 0, first)] },
      );
      break;
    }
  }

  return { firstHoeheMm: Math.round(firstHoehe), flaechen };
}

/**
 * Roof → Welt-Dreiecke der Dachflächen. Trianguliert die Flächen aus `dachRoh()` (SSOT, kein eigener
 * Rechenweg): Quad → 2 Dreiecke, Walm-Dreieck → 1. Nicht-rechteckige Kontur ⇒ DachGeometrieUngueltig
 * (der szene.ts-catch überspringt das Dach dann). firstHoeheMm = Höhe First/oberste Kante über ±0.
 *
 * @throws DachGeometrieUngueltig
 */
export function dachMeshWelt(roof: RoofNode): DachMesh {
  const roh = dachRoh(roof);
  const dreiecke: Dreieck[] = [];
  for (const f of roh.flaechen) {
    const e = f.ecken;
    if (e.length >= 4) {
      dreiecke.push([e[0], e[1], e[2]], [e[0], e[2], e[3]]);
    } else if (e.length === 3) {
      dreiecke.push([e[0], e[1], e[2]]);
    }
  }
  return { dreiecke, firstHoeheMm: roh.firstHoeheMm };
}

/**
 * W-3a: Eine Dachfläche als benannte Rechteck-Ecken (Welt mm, z-up) — Trägerfläche für Aufbauten.
 * eaveLeft/eaveRight liegen an der Traufe, ridgeLeft/ridgeRight am First (bzw. Pult-Oberkante).
 */
export interface DachFlaeche {
  surfaceId: string;
  /** true = achsenparallele Rechteckfläche (Sattel/Pult/Flach) — Voraussetzung fürs echte Gauben-Loch. */
  rechteckig: boolean;
  neigungRad: number;
  eaveLeft: WeltPunkt3;
  eaveRight: WeltPunkt3;
  ridgeRight: WeltPunkt3;
  ridgeLeft: WeltPunkt3;
}

/**
 * Roof → rechteckige Dachflächen (Sattel: 2, Pult/Flach: 1). FILTERT die geteilte `dachRoh()`-Quelle
 * auf die rechteckigen Quads — es sind DIESELBEN Ecken, die `dachMeshWelt` trianguliert (M1/SSOT: keine
 * zweite Herleitung mehr). WALM liefert [] (Trapez/Dreieck = kein sicherer Gauben-Untergrund, Stufe C;
 * Aufbauten dort bleiben Prüf-Marker).
 *
 * @throws DachGeometrieUngueltig bei nicht-rechteckiger Kontur (wie dachMeshWelt).
 */
export function dachflaechen(roof: RoofNode): DachFlaeche[] {
  return dachRoh(roof).flaechen
    .filter((f) => f.rechteckig && f.ecken.length === 4)
    .map((f) => ({
      surfaceId: f.surfaceId,
      rechteckig: true,
      neigungRad: f.neigungRad,
      eaveLeft: f.ecken[0], eaveRight: f.ecken[1], ridgeRight: f.ecken[2], ridgeLeft: f.ecken[3],
    }));
}

/** 3D-Fläche eines Dreiecks in m² (halbe Kreuzprodukt-Norm; mm-Eingabe). */
export function dreieckFlaecheM2(t: Dreieck): number {
  const [a, b, c] = t;
  const ux = b.x - a.x, uy = b.y - a.y, uz = b.z - a.z;
  const vx = c.x - a.x, vy = c.y - a.y, vz = c.z - a.z;
  const cxp = uy * vz - uz * vy;
  const cyp = uz * vx - ux * vz;
  const czp = ux * vy - uy * vx;
  return Math.hypot(cxp, cyp, czp) / 2 / 1_000_000;
}
