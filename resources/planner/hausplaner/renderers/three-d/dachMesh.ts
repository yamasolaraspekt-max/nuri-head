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
  // W-3b: L/T/U (Verschneidungsformen) validieren bereits (Schema), werden aber erst in Stufe 2 über die
  // Verschneidungs-Engine gerendert. Der Kontur-Guard steht EINMAL hier in der geteilten Quelle ⇒ wirkt
  // automatisch für dachMeshWelt (Triangulierung) UND dachflaechen (Filter): leeres Ergebnis statt
  // pauschalem Kontur-Wurf (kein Crash). Rechteckige Formen (inkl. rect): Verhalten unverändert.
  if (istVerschneidungsForm(roof.roofType)) {
    return { firstHoeheMm: Math.round(roof.traufhoeheMm), flaechen: [] };
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
