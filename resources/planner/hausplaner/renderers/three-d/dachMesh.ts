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
import { pruefeRechteckigeKontur } from '../../geometry/dachGeometrie';

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

/**
 * Roof → Welt-Dreiecke der Dachflächen. Nutzt DIESELBE Kontur-Prüfung wie dachFlaechen
 * (pruefeRechteckigeKontur) — nicht-rechteckige Kontur ⇒ DachGeometrieUngueltig, damit Render-Mesh und
 * belastbare Fläche NIE auseinanderlaufen (Kante 1; der szene.ts-catch überspringt das Dach dann).
 * firstHoeheMm = Höhe First/oberste Kante über ±0.
 *
 * @throws DachGeometrieUngueltig
 */
export function dachMeshWelt(roof: RoofNode): DachMesh {
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

  // lokal (u entlang First, v quer, z Höhe) → Welt. KEIN mm-Runden: das Mesh ist abgeleitete
  // Render-Geometrie (wie platziereWandQuader Float-Meter liefert), nicht persistierte mm-Wahrheit.
  const w = (u: number, v: number, z: number): WeltPunkt3 => ({
    x: cx + u * ux + v * vx,
    y: cy + u * uy + v * vy,
    z,
  });

  const dreiecke: Dreieck[] = [];
  const quad = (p1: WeltPunkt3, p2: WeltPunkt3, p3: WeltPunkt3, p4: WeltPunkt3) => {
    dreiecke.push([p1, p2, p3], [p1, p3, p4]);
  };

  let firstHoehe = zt;

  switch (roof.roofType) {
    case 'flach': {
      quad(w(-a, -b, zt), w(a, -b, zt), w(a, b, zt), w(-a, b, zt));
      break;
    }
    case 'pult': {
      const hoch = zt + 2 * b * tan; // Anstieg über die volle Spannweite
      firstHoehe = hoch;
      quad(w(-a, -b, zt), w(a, -b, zt), w(a, b, hoch), w(-a, b, hoch));
      break;
    }
    case 'sattel': {
      const first = zt + b * tan;
      firstHoehe = first;
      // Südseite (v=-b) und Nordseite (v=+b), First bei v=0.
      quad(w(-a, -b, zt), w(a, -b, zt), w(a, 0, first), w(-a, 0, first));
      quad(w(a, b, zt), w(-a, b, zt), w(-a, 0, first), w(a, 0, first));
      break;
    }
    case 'walm': {
      const first = zt + b * tan;
      firstHoehe = first;
      const rr = Math.max(0, a - b); // halbe Firstlänge = (L−B)/2 (Überstand kürzt sich)
      // 2 Haupt-(Trapez-)Flächen …
      quad(w(-a, -b, zt), w(a, -b, zt), w(rr, 0, first), w(-rr, 0, first));
      quad(w(a, b, zt), w(-a, b, zt), w(-rr, 0, first), w(rr, 0, first));
      // … 2 Walm-(Dreiecks-)Flächen an den Giebelenden.
      dreiecke.push([w(a, -b, zt), w(a, b, zt), w(rr, 0, first)]);
      dreiecke.push([w(-a, b, zt), w(-a, -b, zt), w(-rr, 0, first)]);
      break;
    }
  }

  return { dreiecke, firstHoeheMm: Math.round(firstHoehe) };
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
