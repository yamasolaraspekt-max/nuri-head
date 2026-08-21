/**
 * D-b — Dach-Geometrie: reine Funktionen (kein three/React), belastbare Flächen + Azimute je Dachtyp.
 * Grundlage: docs/hausplaner/dach-andock-spec.md §1 (▲D2/▲D4), §3 (Kanten 1–3), §4.1/§4.2 (Abnahme).
 *
 * ▲D4 Azimut-Kontrakt: Nord = +y. Der Flächen-Azimut wird NIE gepflegt, sondern aus der First-Richtung
 * (roof.firstAzimutGrad) abgeleitet. Satteldach: Flächen = First ± 90°. Das ist die belastbare Quelle
 * für PV/Heizlast (Nachfolger des stillgelegten RoofAreaEstimator).
 *
 * ▲D2: die bewiesene Divisions-Schutz-Mathematik (sichererCos) wird wiederverwendet, nicht neu erfunden.
 * V1-Grenze (Kante 1): nur rechteckige Traufkonturen; alles andere wird abgelehnt (nie stilles Falschdach).
 */
import type { RoofNode } from '../domain/scene.types';
// Z1-W1-4: gezogen von `../../utils/dachWerte` auf die Fassung IN der Insel. Beide Dateien waren
// byte-identisch (md5 b5738234…, 103 Z./4188 B); nur die geometry-Fassung ist getestet
// (`__tests__/dachWerte.test.ts:12`), eine Abweichung in der utils-Kopie wäre nirgends aufgefallen.
import { sichererCos } from './dachWerte';
import { walmIstKonsistent } from './dachformVorlagen';
import { polygonFlaecheM2 } from './polygonFlaeche';

export interface DachFlaeche {
  flaeche_m2: number;
  azimut_grad: number | null; // null = horizontal (Flachdach)
  neigung_grad: number;
  first_laenge_mm: number;
}

export class DachGeometrieUngueltig extends Error {
  readonly grund: string;

  constructor(message: string, grund: string) {
    super(message);
    this.name = 'DachGeometrieUngueltig';
    this.grund = grund;
  }
}

type P = { x: number; y: number };

function normAz(grad: number): number {
  return ((grad % 360) + 360) % 360;
}



export interface DachKontur {
  laengeMm: number;
  spannMm: number;
  cx: number; // Schwerpunkt (mm) — für den Mesh-Aufbau
  cy: number;
}

/**
 * EINE Wahrheit für Fläche UND Mesh: Ausdehnung der Kontur entlang der Firstachse (u) und quer (v) +
 * Schwerpunkt, in mm — und die Rechteckigkeits-Prüfung (Kante 1). Wirft DachGeometrieUngueltig, wenn die
 * Traufkontur nicht (nahezu) rechteckig ist, damit belastbare Fläche (dachFlaechen) und Render-Mesh
 * (dachMeshWelt) NIE auseinanderlaufen.
 *
 * @throws DachGeometrieUngueltig
 */
export function pruefeRechteckigeKontur(poly: readonly P[], azGrad: number): DachKontur {
  const rad = (azGrad * Math.PI) / 180;
  const ux = Math.sin(rad);
  const uy = Math.cos(rad); // Firstrichtung (Nord = +y)
  const vx = Math.cos(rad);
  const vy = -Math.sin(rad); // quer zur Firstrichtung
  let sMin = Infinity, sMax = -Infinity, tMin = Infinity, tMax = -Infinity;
  let cx = 0, cy = 0;
  for (const p of poly) {
    const s = p.x * ux + p.y * uy;
    const t = p.x * vx + p.y * vy;
    if (s < sMin) sMin = s;
    if (s > sMax) sMax = s;
    if (t < tMin) tMin = t;
    if (t > tMax) tMax = t;
    cx += p.x;
    cy += p.y;
  }
  const laengeMm = sMax - sMin;
  const spannMm = tMax - tMin;

  // Kante 1: Kontur ~= Bounding-Box, sonst kein stilles Falschdach.
  const bboxM2 = (laengeMm / 1000) * (spannMm / 1000);
  // Z1-W1-3: die private Shoelace-Kopie ist entfallen; gerechnet wird mit `polygonFlaecheM2`.
  // **Einheiten-Vertrag aufgelöst, indem die EINGABE gezogen wird, nicht das Ergebnis:**
  // `polygonFlaeche.ts:11-13` verpflichtet die Funktion auf **Meter**, `poly` liegt in **mm** vor.
  // Umgerechnet wird deshalb hier und sichtbar — ein `/ 1_000_000` auf das Ergebnis wäre zwar
  // rechnerisch gleich (Shoelace ist homogen vom Grad 2), würde den Vertrag der Funktion aber
  // stillschweigend auf mm ausdehnen. Die Datei behält ihre eine Einheiten-Zusage.
  const konturM2 = polygonFlaecheM2(poly.map((p) => ({ x: p.x / 1000, y: p.y / 1000 })));
  if (bboxM2 <= 0 || Math.abs(konturM2 - bboxM2) / bboxM2 > 0.01) {
    throw new DachGeometrieUngueltig(
      'Traufkontur ist nicht rechteckig — V1 unterstützt nur rechteckige Grundrisse (kein stilles Falschdach).',
      'kontur_nicht_rechteckig',
    );
  }

  return { laengeMm, spannMm, cx: poly.length ? cx / poly.length : 0, cy: poly.length ? cy / poly.length : 0 };
}

/**
 * Belastbare Dachflächen (Fläche m², abgeleiteter Azimut, Neigung, First-Länge) eines RoofNode.
 * Rechteckige Traufkontur vorausgesetzt (V1); First parallel zur `firstAzimutGrad`-Richtung.
 * Überstand wirkt an Traufe UND Giebel (Kante 3), d. h. auf Länge UND Spannweite.
 *
 * @throws DachGeometrieUngueltig  wenn die Kontur nicht (nahezu) rechteckig ist (Kante 1).
 */
export function dachFlaechen(roof: RoofNode): DachFlaeche[] {
  // Kante 1 (geteilte Prüfung mit dem Mesh): nicht-rechteckige Kontur ⇒ DachGeometrieUngueltig.
  const { laengeMm, spannMm } = pruefeRechteckigeKontur(roof.polygon, roof.firstAzimutGrad);

  const ue = roof.ueberstandMm;
  const laengeM = (laengeMm + 2 * ue) / 1000; // First-parallel (+ Überstand Giebel)
  const spannM = (spannMm + 2 * ue) / 1000; // quer (+ Überstand Traufe)
  const cos = sichererCos(roof.neigungGrad); // Kante 2: Division geschützt
  const n = roof.neigungGrad;
  const az = roof.firstAzimutGrad;
  const firstMm = Math.round(laengeMm + 2 * ue);

  switch (roof.roofType) {
    case 'flach':
      return [{ flaeche_m2: laengeM * spannM, azimut_grad: null, neigung_grad: 0, first_laenge_mm: firstMm }];

    case 'pult':
      // Eine Fläche; Exposition = firstAzimutGrad (Down-Slope-Richtung, dokumentierte V1-Konvention).
      return [{ flaeche_m2: (spannM / cos) * laengeM, azimut_grad: normAz(az), neigung_grad: n, first_laenge_mm: firstMm }];

    case 'sattel': {
      // Zwei Flächen, je horizontaler Lauf spannM/2; Flächen-Azimut = First ± 90° (▲D4).
      const flaeche = ((spannM / 2) / cos) * laengeM;
      return [
        { flaeche_m2: flaeche, azimut_grad: normAz(az + 90), neigung_grad: n, first_laenge_mm: firstMm },
        { flaeche_m2: flaeche, azimut_grad: normAz(az - 90), neigung_grad: n, first_laenge_mm: firstMm },
      ];
    }

    case 'walm': {
      // Z1-W1-2 (Y-1 ENTSCHIEDEN 21.08.: ABLEHNEN, keine stille Dreh-Korrektur).
      // **Warum hier gesperrt wird:** `Math.max(0, laengeM - spannM)` klemmte die Firstlänge still
      // auf 0, während die Walm-Dreiecke auf voller Giebelbreite blieben — die Summe wuchs damit
      // unbegrenzt über den Erhaltungssatz (Σ Facetten = Grundriss / cos α) hinaus. Gemessen bei 30°:
      // 6×8 m → 64,66 statt 55,43 m² (+16,7 %), 4×10 m → 80,83 statt 46,19 m² (+75,0 %);
      // 3×12 m bei 35° → 109,87 statt 43,95 m² (+150 %). Diese Fläche speist PV-Ertrag und Heizlast.
      //
      // **Zur Auflage „walmIstKonsistent benutzen, keine dritte Fassung":** sie wird benutzt — aber
      // sie verlangt `L > B` und weist damit auch den GLEICHSTAND ab. `L === B` ist jedoch ein
      // gültiges **Zeltdach** und rechnet heute exakt: nachgemessen bei 30°, 35° und 45° je ±0,0 %
      // gegen den Erhaltungssatz. Ein Wurf dort wäre eine Fehlsperre gegen einen funktionierenden
      // Fall. Gesperrt wird deshalb nur der gemessene Defektrand `spannM > laengeM`; die Regel
      // selbst bleibt die eine Fassung in `dachformVorlagen.ts:414-416`.
      if (!walmIstKonsistent(laengeM, spannM) && laengeM !== spannM) {
        throw new DachGeometrieUngueltig(
          'Walmdach: Giebelbreite größer als Gebäudelänge — die Firstlänge wäre negativ. ' +
            'Firstrichtung anders legen (kein stilles Falschdach).',
          'walm_giebelbreite_ueber_laenge',
        );
      }
      // Firstlänge = L − B (symmetrischer Walm, gleiche Neigung ringsum); 2 Trapez- + 2 Walm-(Dreiecks-)Flächen.
      const firstLenM = Math.max(0, laengeM - spannM);
      const trapez = ((laengeM + firstLenM) / 2) * ((spannM / 2) / cos);
      const walm = (spannM * spannM) / (4 * cos);
      const firstLenMm = Math.round(firstLenM * 1000);
      return [
        { flaeche_m2: trapez, azimut_grad: normAz(az + 90), neigung_grad: n, first_laenge_mm: firstLenMm },
        { flaeche_m2: trapez, azimut_grad: normAz(az - 90), neigung_grad: n, first_laenge_mm: firstLenMm },
        { flaeche_m2: walm, azimut_grad: normAz(az), neigung_grad: n, first_laenge_mm: firstLenMm },
        { flaeche_m2: walm, azimut_grad: normAz(az + 180), neigung_grad: n, first_laenge_mm: firstLenMm },
      ];
    }

    default:
      // W-3b: rect/l/t/u-shape — zusammengesetzte/rechteckige Neuformen. Flächenprojektion (inkl.
      // Verschneidungsflächen) folgt in Stufe 2; hier bewusst keine Fläche statt eines Rateswerts.
      return [];
  }
}
