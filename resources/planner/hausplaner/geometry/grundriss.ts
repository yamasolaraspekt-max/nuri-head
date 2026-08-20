/**
 * Eingabeaufforderung 13: reine, testbare Geometrie für ZUSAMMENGESETZTE Gebäudegrundrisse
 * (L-, T-, U-Form). Liefert das Grundrisspolygon als EINZELNES, nicht-selbstüberschneidendes
 * Polygon (keine überlappenden Rechtecke -> keine Doppelzählung der Grundfläche), erkennt
 * einspringende Innenwinkel (Kehlen-Kandidaten bei geneigten Dächern) und vorspringende
 * Außenecken und berechnet die Fläche über die Gaußsche Flächenformel (Reparatur 6).
 *
 * Die Engine (buildFlat) baut daraus EINE Dachfläche 'main' mit genau diesem Polygon — die
 * Flächen-, Aufmaß- und Aufbau-Logik arbeitet dann auf dem echten Polygon statt auf width×height.
 *
 * Koordinaten 0-basiert in Metern: x ∈ [0, L] (Länge), y ∈ [0, B] (Breite). Maße:
 *  - L = Gesamtlänge, B = Gesamtbreite (Bounding-Box des Baukörpers)
 *  - lengthB = Tiefe des Seitenflügels/Stiels in x-Richtung (L-/T-/U-Bein-Breite)
 *  - widthB = Tiefe des Quer-/Basisriegels in y-Richtung
 *
 * Rein (keine React-/THREE-Abhängigkeit). KEINE Dacheindeckung/Material.
 
 *
 * ---
 *
 * **HERKUNFTSVERMERK, nachgetragen 20.08. — dieser Kopf nennt einen Wirt, den es hier nicht gibt.**
 *
 * Die oben genannte „Engine" ist `class RoofEngine`, und sie steht **ausschliesslich** in
 * `docs/planner/pv-belegung-referenz/DachplanerProPage.tsx:369` — einer Referenzdatei unter `docs/`,
 * nicht im Produktivbaum. Gemessen: `buildFlat`, `ObstacleData` und `RoofEngine` haben in
 * `resources/` und `app/` **null Definitionen**, nur Kommentar-Nennungen.
 *
 * **Folge:** dieses Modul hat heute keinen Produktivverbraucher, und der Aufrufer, gegen den es
 * geschrieben ist, wurde nie mitgebracht. Es ist nicht „gebaut und noch nicht angeschlossen",
 * sondern **gegen ein anderes Haus gebaut**.
 *
 * **Dazu die Einheiten:** dieses Modul rechnet in **Metern** (3 Feldnamen auf `…M`/`…M2`, null auf
 * `…Mm`); die Domaene dieser Insel fuehrt **ganze Millimeter** (`domain/scene.types.ts:270`). Ein
 * Anschluss ist damit keine Verdrahtung, sondern eine Umrechnung mit Rundungsentscheidung.
 *
 * **Wer es anschliessen will, muss zuerst diesen Vermerk entkraeften** — sonst wird aus einem
 * fremden Vertrag stillschweigend ein eigener.
 */

import { polygonFlaecheM2, type Punkt2D } from './polygonFlaeche';

export type GrundrissForm = 'rechteck' | 'l-form' | 't-form' | 'u-form';

function endlich(n: unknown, fb = 0): number {
  return typeof n === 'number' && Number.isFinite(n) ? n : fb;
}
function clampLeg(leg: number, gesamt: number): number {
  // Bein-/Riegelmaß muss > 0 und < Gesamtmaß bleiben (sonst entartet die Form zum Rechteck/leer).
  const g = Math.max(0.2, gesamt);
  return Math.min(g - 0.1, Math.max(0.1, leg));
}

/**
 * Grundrisspolygon (0-basiert, gegen den Uhrzeigersinn) für die jeweilige Form.
 * rechteck: 4 Ecken. l/t/u: 6/8/8 Ecken mit einspringenden Innenwinkeln.
 * Entartete Maße werden konservativ geklemmt; ungültige Eingaben -> Rechteck.
 */
export function grundrissPolygon(form: GrundrissForm, length: number, width: number, lengthB?: number, widthB?: number): Punkt2D[] {
  const L = Math.max(0.2, endlich(length, 1));
  const B = Math.max(0.2, endlich(width, 1));
  const LB = clampLeg(endlich(lengthB, L * 0.45), L);
  const WB = clampLeg(endlich(widthB, B * 0.45), B);

  if (form === 'l-form') {
    // Voller Basisriegel unten (Höhe WB) + linker Seitenflügel (Breite LB) bis B.
    return [
      { x: 0, y: 0 }, { x: L, y: 0 }, { x: L, y: WB },
      { x: LB, y: WB }, { x: LB, y: B }, { x: 0, y: B },
    ];
  }
  if (form === 't-form') {
    // Voller Querriegel unten (Höhe WB) + mittiger Stiel (Breite LB) bis B.
    const sl = (L - LB) / 2, sr = (L + LB) / 2;
    return [
      { x: 0, y: 0 }, { x: L, y: 0 }, { x: L, y: WB },
      { x: sr, y: WB }, { x: sr, y: B }, { x: sl, y: B }, { x: sl, y: WB }, { x: 0, y: WB },
    ];
  }
  if (form === 'u-form') {
    // Voller Basisriegel unten (Höhe WB) + linker & rechter Flügel (je Breite LBu) bis B,
    // offene Mitte (Innenhof) zwischen den Flügeln. WICHTIG: 2·LBu < L, sonst überlappen die
    // Flügel und das Polygon würde sich selbst überschneiden -> Flügelbreite konservativ klemmen.
    const LBu = Math.min(LB, (L - 0.2) / 2);
    return [
      { x: 0, y: 0 }, { x: L, y: 0 }, { x: L, y: B },
      { x: L - LBu, y: B }, { x: L - LBu, y: WB }, { x: LBu, y: WB }, { x: LBu, y: B }, { x: 0, y: B },
    ];
  }
  // rechteck
  return [{ x: 0, y: 0 }, { x: L, y: 0 }, { x: L, y: B }, { x: 0, y: B }];
}

/** Signierte Polygonfläche (Shoelace). >0 = gegen Uhrzeigersinn (CCW). */
function signierteFlaeche(poly: ReadonlyArray<Punkt2D>): number {
  let a = 0;
  for (let i = 0; i < poly.length; i++) {
    const p = poly[i], q = poly[(i + 1) % poly.length];
    a += p.x * q.y - q.x * p.y;
  }
  return a / 2;
}

export interface EckenAnalyse {
  /** einspringende (konkave) Ecken = Innenwinkel > 180° (Kehlen-Kandidaten bei geneigten Dächern). */
  innenwinkel: Punkt2D[];
  /** vorspringende (konvexe) Außenecken. */
  aussenecken: Punkt2D[];
}

/** Klassifiziert jede Ecke als einspringend (innen) oder vorspringend (außen). */
export function eckenAnalyse(poly: ReadonlyArray<Punkt2D>): EckenAnalyse {
  const innen: Punkt2D[] = [];
  const aussen: Punkt2D[] = [];
  const n = poly.length;
  if (n < 3) return { innenwinkel: innen, aussenecken: aussen };
  const orient = signierteFlaeche(poly) >= 0 ? 1 : -1; // CCW=+1
  for (let i = 0; i < n; i++) {
    const p = poly[(i - 1 + n) % n], c = poly[i], q = poly[(i + 1) % n];
    const cross = (c.x - p.x) * (q.y - c.y) - (c.y - p.y) * (q.x - c.x);
    if (cross * orient < -1e-9) innen.push({ x: c.x, y: c.y });   // einspringend
    else if (cross * orient > 1e-9) aussen.push({ x: c.x, y: c.y }); // vorspringend
  }
  return { innenwinkel: innen, aussenecken: aussen };
}

/** Anzahl einspringender Innenwinkel. */
export function anzahlInnenwinkel(poly: ReadonlyArray<Punkt2D>): number {
  return eckenAnalyse(poly).innenwinkel.length;
}

/** Echte Grundrissfläche über das Polygon (Shoelace) — NIE width×height bei L/T/U. */
export function grundrissFlaecheM2(poly: ReadonlyArray<Punkt2D>): number {
  return polygonFlaecheM2(poly);
}

/** true, wenn das Polygon KEIN einfaches Rechteck ist (zusammengesetzter Grundriss). */
export function istZusammengesetzt(poly: ReadonlyArray<Punkt2D>): boolean {
  return poly.length > 4 || anzahlInnenwinkel(poly) > 0;
}

/** Form -> erwartete Anzahl einspringender Innenwinkel (L=1, T=2, U=2, Rechteck=0). */
export function erwarteteInnenwinkel(form: GrundrissForm): number {
  return form === 'l-form' ? 1 : (form === 't-form' || form === 'u-form') ? 2 : 0;
}

/** Bilingual: Vorlagen-shapeKey/engineShape -> GrundrissForm. */
export function formAusShape(shape: string): GrundrissForm {
  if (shape === 'l-shape') return 'l-form';
  if (shape === 't-shape') return 't-form';
  if (shape === 'u-shape' || shape === 'u-grundriss') return 'u-form';
  return 'rechteck';
}

export type { Punkt2D };
