/**
 * Eingabeaufforderung 18: REINE, testbare Hilfslogik für MASSHALTIGE Dachöffnungen und sichere
 * Dachflächen-Ausschnitte (Gaube, Dachfenster, Kamin, Lichtkuppel, Lüfter).
 *
 * STUFENMODELL (bewusst risikoarm):
 *  - Stufe A: Öffnung maßhaltig in Dachflächenkoordinaten (u/v) berechnen + als Prüffeld führen.
 *  - Stufe B: NUR für einfache RECHTECKIGE Dachflächen (Sattel/Pult/Flach) und einfache
 *    Durchdringungen (Dachfenster/Kamin/Lüfter/Lichtkuppel) einen ECHTEN, maßhaltigen Ausschnitt
 *    mit Restflächen + Flächenabzug erzeugen — sonst bleibt es beim Prüffeld.
 *  - Stufe C (NICHT hier): echte Polygonloch-/CSG-Operationen für Walm/Trapez/L-T-U und
 *    Gaubendach-Kehlverschneidung.
 *
 * Diese Util ZERSTÖRT NICHTS: sie liefert nur Geometrie/Flächen/Status. Ob die Engine daraus einen
 * sichtbaren Ausschnitt macht, entscheidet sie anhand von `echterAusschnitt`. Brutto-Polygon und
 * Reparatur-6-Flächenlogik bleiben unberührt.
 *
 * Koordinaten: u = parallel Traufe (Bounding-Box-Breite), v = Traufe->First (geneigte Höhe). Maße
 * konsistent zu aufbauPlatzierung/dachOeffnung. WICHTIG (Verwechslungsschutz):
 *   Breite  = u-Richtung (o.width)
 *   Höhe    = v-Richtung (Dachfenster: o.height; sonst o.depth = Schrägen-Footprint)  <- Öffnungshöhe
 *   Tiefe   = Aufbauhöhe des Körpers (steht über der Fläche) — NICHT die Öffnungshöhe.
 *
 * Rein (keine THREE-/React-Abhängigkeit). KEINE Dacheindeckung, KEINE Statik, KEINE Produktwahl.
 
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
 * **Dazu die Einheiten:** dieses Modul rechnet in **Metern** (80 Feldnamen auf `…M`/`…M2`, null auf
 * `…Mm`); die Domaene dieser Insel fuehrt **ganze Millimeter** (`domain/scene.types.ts:270`). Ein
 * Anschluss ist damit keine Verdrahtung, sondern eine Umrechnung mit Rundungsentscheidung.
 *
 * **Wer es anschliessen will, muss zuerst diesen Vermerk entkraeften** — sonst wird aus einem
 * fremden Vertrag stillschweigend ein eigener.
 */

import { polygonFlaecheM2, type Punkt2D } from './polygonFlaeche';
import { oeffnungRechteck, type OeffnungEingabe } from './dachOeffnung';
import { flaecheInfoAusPolygon } from './linienBauteile';
// Eingabeaufforderung 25: realer Gauben-Fußabdruck als (u,v)-Polygon (Pentagon) für das Hauptdach-Loch.
import { fussabdruckUV } from './gaubeGeometrie';

export type AusschnittStatus = 'sicher' | 'teilweise' | 'pruefpflichtig';

export interface OeffnungRechteckM { uMin: number; uMax: number; vMin: number; vMax: number; }

export interface AusschnittBefund {
  surfaceId: string;
  art: string;
  status: AusschnittStatus;
  /** true = Stufe B: echter maßhaltiger Ausschnitt mit Flächenabzug (sicher). */
  echterAusschnitt: boolean;
  /** true = Öffnung (inkl. Rand) liegt vollständig in der Dachfläche. */
  innerhalb: boolean;
  /** Öffnungsrechteck (inkl. Sicherheitsrand) in Metern, Dachflächenkoordinaten. */
  rechteckM: OeffnungRechteckM;
  /** Eckpunkte des Prüffelds in lokalen Dachflächen-(u,v)-Metern (CCW). */
  eckenLokal: Punkt2D[];
  breiteM: number;  // u-Richtung (reine Öffnung ohne Rand)
  hoeheM: number;   // v-Richtung (reine Öffnung ohne Rand)
  sicherheitsrandM: number;
  oeffnungFlaecheM2: number;
  bruttoFlaecheM2: number;
  /** Netto = Brutto − Öffnung NUR wenn echterAusschnitt; sonst = Brutto (kein Abzug). */
  nettoFlaecheM2: number;
  /** Restflächen-Teilpolygone (nur bei echtem Rechteck-Ausschnitt; sonst leer). */
  restPolygone: Punkt2D[][];
  /** E25: realer Gauben-Fußabdruck als (u,v)-Polygon — wenn gesetzt, wird das Loch als Polygon (nicht
   *  Rechteck) geschnitten und oeffnungFlaecheM2 ist die Polygonfläche (Shoelace). Sonst Rechteck. */
  poly?: Punkt2D[];
  warnungen: string[];
  grund: string;
}

function endlich(n: unknown, fb = 0): number { return typeof n === 'number' && Number.isFinite(n) ? n : fb; }
function r3(n: number): number { return Math.round(n * 1000) / 1000; }

/**
 * true, wenn das Polygon ein ACHSENPARALLELES Rechteck ist (Sattel-/Pult-/Flach-Rechteckfläche):
 * genau 4 Ecken, exakt 2 verschiedene u- und 2 verschiedene v-Werte. Trapez (Walm), Dreieck und
 * zusammengesetzte L-/T-/U-Polygone sind damit ausgeschlossen.
 */
export function istAchsenRechteck(poly: ReadonlyArray<Punkt2D>, tol = 1e-4): boolean {
  if (!poly || poly.length !== 4) return false;
  const xs = Array.from(new Set(poly.map((p) => Math.round(p.x / tol)))).length;
  const ys = Array.from(new Set(poly.map((p) => Math.round(p.y / tol)))).length;
  return xs === 2 && ys === 2;
}

/** Eingabeaufforderung 20: zusätzlicher Mindestabstand der Öffnung zu JEDER (auch schrägen) Dachkante. */
export const KANTEN_RAND_M = 0.2;

function signierteFlaeche(poly: ReadonlyArray<Punkt2D>): number {
  let a = 0;
  for (let i = 0; i < poly.length; i++) {
    const p = poly[i], q = poly[(i + 1) % poly.length];
    a += p.x * q.y - q.x * p.y;
  }
  return a / 2;
}

/**
 * Eingabeaufforderung 20: true, wenn das Polygon ein EINFACHES, KONVEXES Viereck mit 4 verschiedenen
 * Ecken und positiver Fläche ist (keine Selbstüberschneidung, kein einspringender Winkel, kein
 * entartetes Dreieck). Grundlage für sichere Trapez-/Walmflächen.
 */
export function istKonvexesViereck(poly: ReadonlyArray<Punkt2D>, tol = 1e-6): boolean {
  if (!poly || poly.length !== 4) return false;
  for (let i = 0; i < 4; i++) for (let j = i + 1; j < 4; j++) {
    if (Math.hypot(poly[i].x - poly[j].x, poly[i].y - poly[j].y) < 1e-4) return false; // doppelte Ecke -> Dreieck
  }
  const area = signierteFlaeche(poly);
  if (Math.abs(area) < tol) return false;
  const orient = Math.sign(area);
  for (let i = 0; i < 4; i++) {
    const a = poly[(i + 3) % 4], b = poly[i], c = poly[(i + 1) % 4];
    const cross = (b.x - a.x) * (c.y - b.y) - (b.y - a.y) * (c.x - b.x);
    if (cross * orient <= tol) return false; // konkav/kollinear -> nicht sicher konvex
  }
  return true;
}

function kantenParallel(u: Punkt2D, v: Punkt2D, sinTol = 0.14): boolean {
  const lu = Math.hypot(u.x, u.y), lv = Math.hypot(v.x, v.y);
  if (lu < 1e-9 || lv < 1e-9) return false;
  return Math.abs(u.x * v.y - u.y * v.x) / (lu * lv) <= sinTol; // ~8° Toleranz
}

/**
 * Eingabeaufforderung 20: sichere Trapez-/Walmfläche = konvexes Viereck mit mindestens EINEM Paar
 * (annähernd) paralleler gegenüberliegender Kanten (Traufe ∥ First). Rechtecke erfüllen das ebenfalls.
 */
export function istSichereTrapezflaeche(poly: ReadonlyArray<Punkt2D>): boolean {
  if (!istKonvexesViereck(poly)) return false;
  const e = (i: number, j: number): Punkt2D => ({ x: poly[j].x - poly[i].x, y: poly[j].y - poly[i].y });
  return kantenParallel(e(0, 1), e(3, 2)) || kantenParallel(e(1, 2), e(0, 3));
}

/**
 * Eingabeaufforderung 21: STRENG konvexes, EINFACHES Polygon mit >= minEcken (4, auch 5/6/...).
 * Akzeptiert Rechteck, Trapez und konvexe n-Ecke. Lehnt sicher ab: konkave Flächen (L/T/U mit
 * einspringendem Innenwinkel), selbstüberschneidende Polygone, Dreiecke/Entartungen, doppelte Ecken,
 * Nullkanten, kollineare Folgepunkte und Mehrfach-Umrundungen.
 *
 * Kriterien: alle Punkte endlich; keine zwei (nahezu) gleichen Ecken; keine Kante < 1e-4 m; positive
 * Fläche; ALLE aufeinanderfolgenden Kanten drehen im selben Sinn (kein einspringender Winkel ->
 * schließt L/T/U aus); Summe der Außenwinkel = ±2π (genau EINE Umrundung -> schließt Selbstschnitt/
 * Sternformen aus).
 */
export function istSichereKonvexeFlaeche(poly: ReadonlyArray<Punkt2D>, minEcken = 4): boolean {
  if (!poly || poly.length < Math.max(3, minEcken)) return false;
  const n = poly.length;
  for (const p of poly) if (!Number.isFinite(p.x) || !Number.isFinite(p.y)) return false;
  // keine zwei (nahezu) identischen Ecken (degeneriert / als Dreieck getarntes Viereck)
  for (let i = 0; i < n; i++) for (let j = i + 1; j < n; j++) {
    if (Math.hypot(poly[i].x - poly[j].x, poly[i].y - poly[j].y) < 1e-4) return false;
  }
  // keine Nullkanten
  for (let i = 0; i < n; i++) {
    const a = poly[i], b = poly[(i + 1) % n];
    if (Math.hypot(b.x - a.x, b.y - a.y) < 1e-4) return false;
  }
  const area = signierteFlaeche(poly);
  if (Math.abs(area) < 1e-6) return false;
  const orient = Math.sign(area);
  let turning = 0;
  for (let i = 0; i < n; i++) {
    const a = poly[(i + n - 1) % n], b = poly[i], c = poly[(i + 1) % n];
    const e1x = b.x - a.x, e1y = b.y - a.y;
    const e2x = c.x - b.x, e2y = c.y - b.y;
    const cross = e1x * e2y - e1y * e2x;
    if (cross * orient <= 1e-9) return false; // einspringend/kollinear -> nicht streng konvex (L/T/U raus)
    turning += Math.atan2(cross, e1x * e2x + e1y * e2y);
  }
  return Math.abs(Math.abs(turning) - 2 * Math.PI) <= 1e-3; // genau eine Umrundung
}

/**
 * Eingabeaufforderung 22: zerlegt eine KONKAVE, ACHSENPARALLELE (rektilineare) Fläche — typisch
 * L-/T-/U-Flachdach — per Vertikal-Slab-Verfahren in nicht-überlappende KONVEXE Teilrechtecke.
 * Sicherer, kontrollierter Einstieg in konkave Loch-Logik: KEINE allgemeine/riskante Polygonloch-
 * Operation. Gibt [] zurück, wenn die Fläche NICHT sicher zerlegbar ist (schräge Kante, Nullkante,
 * nicht-endliche Werte, Selbstschnitt/Flächen-Mismatch). Summe der Teilflächen == Polygonfläche
 * wird verifiziert (keine Lücke/Überlappung/Doppelzählung), sonst [].
 *
 * L-Form -> 2 Rechtecke, T-Form -> 3, U-Form -> 3 (ergibt sich automatisch aus den x-Schnitten).
 */
export function konvexeTeilflaechenSicher(poly: ReadonlyArray<Punkt2D>, tol = 1e-4): Punkt2D[][] {
  if (!poly || poly.length < 6) return []; // L/T/U haben >= 6 Ecken (konvex wird separat behandelt)
  const n = poly.length;
  for (const p of poly) if (!Number.isFinite(p.x) || !Number.isFinite(p.y)) return [];
  // ALLE Kanten achsenparallel + keine Nullkante (sonst nicht sicher rektilinear)
  for (let i = 0; i < n; i++) {
    const a = poly[i], b = poly[(i + 1) % n];
    const dx = Math.abs(b.x - a.x), dy = Math.abs(b.y - a.y);
    if (dx < tol && dy < tol) return [];   // Nullkante
    if (dx > tol && dy > tol) return [];   // schräge/diagonale Kante -> nicht rektilinear
  }
  const polyArea = Math.abs(signierteFlaeche(poly));
  if (!(polyArea > tol)) return [];
  // distinkte, sortierte x-Werte -> vertikale Streifen
  const xs: number[] = [];
  for (const x of poly.map((p) => p.x).sort((a, b) => a - b)) {
    if (!xs.length || Math.abs(x - xs[xs.length - 1]) > tol) xs.push(x);
  }
  if (xs.length < 2) return [];
  const rects: Punkt2D[][] = [];
  let summe = 0;
  for (let i = 0; i < xs.length - 1; i++) {
    const x0 = xs[i], x1 = xs[i + 1], xm = (x0 + x1) / 2;
    if (x1 - x0 < tol) continue;
    // Innere y-Intervalle bei xm: y-Werte horizontaler Kanten, die xm überspannen (Even-Odd-Regel)
    const ys: number[] = [];
    for (let j = 0; j < n; j++) {
      const a = poly[j], b = poly[(j + 1) % n];
      if (Math.abs(a.y - b.y) < tol) {
        const lo = Math.min(a.x, b.x), hi = Math.max(a.x, b.x);
        if (lo < xm && xm < hi) ys.push(a.y);
      }
    }
    ys.sort((a, b) => a - b);
    for (let k = 0; k + 1 < ys.length; k += 2) {
      const y0 = ys[k], y1 = ys[k + 1];
      if (y1 - y0 > tol) {
        rects.push([{ x: x0, y: y0 }, { x: x1, y: y0 }, { x: x1, y: y1 }, { x: x0, y: y1 }]);
        summe += (x1 - x0) * (y1 - y0);
      }
    }
  }
  if (rects.length < 2) return []; // konkave Fläche muss in >= 2 Teilrechtecke zerfallen
  if (Math.abs(summe - polyArea) > Math.max(tol, polyArea * 1e-4)) return []; // Lücke/Überlappung -> unsicher
  return rects;
}

/**
 * Eingabeaufforderung 20: prüft, ob ALLE Eckpunkte mit Mindestabstand `margin` INNERHALB des konvexen
 * Polygons liegen (auch gegen schräge Kanten). Funktioniert für Rechteck UND Trapez (CCW/CW-egal).
 */
export function rechteckKantenAbstandOk(ecken: ReadonlyArray<Punkt2D>, poly: ReadonlyArray<Punkt2D>, margin: number): boolean {
  if (!poly || poly.length < 3) return false;
  const orient = Math.sign(signierteFlaeche(poly)) || 1;
  for (const p of ecken) {
    for (let i = 0; i < poly.length; i++) {
      const a = poly[i], b = poly[(i + 1) % poly.length];
      const ex = b.x - a.x, ey = b.y - a.y;
      const len = Math.hypot(ex, ey) || 1;
      const abstand = ((ex * (p.y - a.y) - ey * (p.x - a.x)) * orient) / len; // >0 = innen
      if (abstand < margin) return false;
    }
  }
  return true;
}

/** Punkt-in-Polygon (Ray-Casting), Rand zählt als innen (mit kleiner Toleranz). */
function punktInPolygon(p: Punkt2D, poly: ReadonlyArray<Punkt2D>, tol = 1e-6): boolean {
  let inside = false;
  for (let i = 0, j = poly.length - 1; i < poly.length; j = i++) {
    const a = poly[i], b = poly[j];
    const schneidet = (a.y > p.y + tol) !== (b.y > p.y + tol)
      && p.x < ((b.x - a.x) * (p.y - a.y)) / ((b.y - a.y) || 1e-12) + a.x;
    if (schneidet) inside = !inside;
  }
  return inside;
}

/** Achsenparallele Rest-Teilrechtecke eines Rechtecks [u0,u1]×[v0,v1] MINUS Loch [a,b]×[c,d]. */
function restRechtecke(u0: number, u1: number, v0: number, v1: number, a: number, b: number, c: number, d: number): Punkt2D[][] {
  const rects: Punkt2D[][] = [];
  const push = (x0: number, x1: number, y0: number, y1: number) => {
    if (x1 - x0 > 1e-6 && y1 - y0 > 1e-6) rects.push([{ x: x0, y: y0 }, { x: x1, y: y0 }, { x: x1, y: y1 }, { x: x0, y: y1 }]);
  };
  push(u0, u1, v0, c);   // unten (Traufseite)
  push(u0, u1, d, v1);   // oben (Firstseite)
  push(u0, a, c, d);     // links
  push(b, u1, c, d);     // rechts
  return rects;
}

/** Aufbauarten, die als EINFACHE Durchdringung einen echten Rechteck-Ausschnitt erlauben (keine Gauben). */
export function istEinfacheDurchdringung(art: string): boolean {
  return art === 'window' || art === 'chimney' || art === 'vent' || art === 'lichtkuppel';
}

/**
 * Eingabeaufforderung 23: Gauben-Durchdringung (Schlepp-/Pult-/Trapez- und Giebel-/Spitzgaube).
 * Erzeugt ein echtes Hauptdach-Loch unter dem Gaubenfußabdruck NUR auf rechteckiger, geneigter
 * Sattel-/Pultfläche (Aufrufer setzt gaubeErlaubt). Walm/Trapez/L-T-U/Sonderflächen sowie Tonnen-/
 * Fledermaus-/Zwerch-/Rund-/Walmdachgauben bleiben Prüffeld/geplant.
 */
export function istGaubeDurchdringung(art: string): boolean {
  return art === 'schleppgaube' || art === 'flachgaube' || art === 'trapezgaube'
    || art === 'giebelgaube' || art === 'spitzgaube';
}

/**
 * Berechnet die maßhaltige Öffnung + (falls sicher) den echten Rechteck-Ausschnitt einer Dachfläche.
 * `polygon` = Dachflächenpolygon in (u,v)-Metern; `width/height` = Bounding-Box (surf.width/height).
 * Niemals NaN/Infinity/negativ; bei Unsicherheit -> echterAusschnitt=false (Prüffeld bleibt).
 */
export function berechneAusschnitt(
  surfaceId: string,
  polygon: ReadonlyArray<Punkt2D>,
  width: number,
  height: number,
  o: OeffnungEingabe,
  sicherheitsrandM = 0.1,
  gaubeErlaubt = false, // E23: Fläche ist geneigte, rechteckige Sattel-/Pultfläche -> Gauben-Loch erlaubt
  aRad = 0,             // E25: Dachneigung (für den realen Gauben-Fußabdruck als Polygon)
): AusschnittBefund {
  const W = Math.max(0.01, endlich(width));
  const H = Math.max(0.01, endlich(height));
  const poly = (polygon && polygon.length >= 3) ? polygon.map((p) => ({ x: endlich(p.x), y: endlich(p.y) })) : [];
  const fl = flaecheInfoAusPolygon(surfaceId, W, H, poly);
  const rect = oeffnungRechteck(o, fl, sicherheitsrandM); // maßhaltig (rel.) + innerhalb-Flag

  const uMin = rect.uMinRel * W, uMax = rect.uMaxRel * W;
  const vMin = rect.vMinRel * H, vMax = rect.vMaxRel * H;
  const eckenLokal: Punkt2D[] = [
    { x: uMin, y: vMin }, { x: uMax, y: vMin }, { x: uMax, y: vMax }, { x: uMin, y: vMax },
  ];
  // Öffnungsfläche = Prüffeld inkl. Rand (maßhaltig). Reine Öffnung (ohne Rand) separat in breite/hoehe.
  // E25: bei echtem Gauben-Polygonloch wird oeffnungFlaecheM2 auf die Polygonfläche (Shoelace) gesetzt.
  let oeffnungFlaecheM2 = Math.max(0, (uMax - uMin) * (vMax - vMin));
  let polyFootprint: Punkt2D[] | undefined;
  const bruttoFlaecheM2 = poly.length >= 3 ? Math.max(0, polygonFlaecheM2(poly)) : W * H;

  const warnungen: string[] = [];
  const rechteckig = istAchsenRechteck(poly);
  // E19 Rechteck, E20 Trapez, E21 beliebige sichere konvexe n-Eck-Fläche (>=4 Ecken) — alle subsumiert.
  const konvex = istSichereKonvexeFlaeche(poly);
  // E22: konkave, rektilineare L-/T-/U-Fläche -> sichere konvexe Teilrechtecke (sonst []).
  const teilflaechen = konvex ? [] : konvexeTeilflaechenSicher(poly);
  const sichereFlaeche = konvex || teilflaechen.length > 0;
  const einfach = istEinfacheDurchdringung(o.art);
  // E23: Gaube erzeugt ein echtes Hauptdach-Loch (unter dem Gaubenfußabdruck) NUR auf rechteckiger,
  // geneigter Sattel-/Pultfläche (gaubeErlaubt vom Aufrufer). Auf Walm/Trapez/konvexen n-Ecken und
  // L-/T-/U bleibt die Gaube Prüffeld.
  const istGaube = istGaubeDurchdringung(o.art);
  const gaubeLochbar = istGaube && gaubeErlaubt && rechteckig;
  const durchdringungLochbar = einfach || gaubeLochbar;
  // Öffnung muss inkl. Rand vollständig in der Fläche liegen (Bounding-Box + Punkt-in-Polygon).
  const innerhalb = rect.innerhalb && eckenLokal.every((p) => punktInPolygon(p, poly));
  // E20/E21: Mindestabstand zu JEDER (auch schrägen) Kante. E22: bei konkaver Fläche muss die Öffnung
  // mit Randabstand vollständig in GENAU EINEM konvexen Teilbereich liegen (nicht über Innenkanten/
  // Innenhof/einspringenden Innenwinkel) -> Abstand zu allen Kanten eines Teilrechtecks.
  const sicherInnen = innerhalb && (
    konvex
      ? rechteckKantenAbstandOk(eckenLokal, poly, KANTEN_RAND_M)
      : teilflaechen.some((r) => rechteckKantenAbstandOk(eckenLokal, r, KANTEN_RAND_M))
  );

  let echterAusschnitt = false;
  let status: AusschnittStatus = 'pruefpflichtig';
  let restPolygone: Punkt2D[][] = [];
  let nettoFlaecheM2 = bruttoFlaecheM2;
  let grund = '';

  if (!innerhalb) {
    status = 'pruefpflichtig';
    grund = 'Öffnung liegt (teilweise) außerhalb der Dachfläche — prüfpflichtig, kein Ausschnitt.';
    warnungen.push(grund);
  } else if (oeffnungFlaecheM2 <= 1e-6 || oeffnungFlaecheM2 >= bruttoFlaecheM2 * 0.9) {
    status = 'pruefpflichtig';
    grund = 'Öffnungsmaß unplausibel (zu klein oder zu groß) — prüfpflichtig, kein Ausschnitt.';
    warnungen.push(grund);
  } else if (sichereFlaeche && durchdringungLochbar && sicherInnen) {
    // Stufe B: sicherer, maßhaltiger Ausschnitt mit Flächenabzug — Rechteck (EA19), konvexe Trapez-/
    // Walmfläche (EA20/21), L-/T-/U-Teilbereich (EA22) oder Gaubenfußabdruck auf Rechteckfläche (EA23).
    echterAusschnitt = true;
    status = 'sicher';
    if (rechteckig) {
      const u0 = Math.min(...poly.map((p) => p.x)), u1 = Math.max(...poly.map((p) => p.x));
      const v0 = Math.min(...poly.map((p) => p.y)), v1 = Math.max(...poly.map((p) => p.y));
      restPolygone = restRechtecke(u0, u1, v0, v1, uMin, uMax, vMin, vMax);
    } else {
      restPolygone = []; // Trapez-Restzerlegung ist komplex und nur informativ -> weglassen (Netto exakt via Fläche)
    }
    // E25: Gaube -> realer Fußabdruck als (u,v)-Polygon (Pult=4-Eck, Giebel=5-Eck, Spitz=3-Eck). Nur wenn
    // das Polygon einfach-konvex, mit Randabstand vollständig in der Fläche und plausibel ist; sonst
    // Rückfall auf das Rechteck-Loch (poly bleibt undefined).
    if (gaubeLochbar && aRad > 1e-6) {
      const fp = fussabdruckUV(
        { type: o.art, x: o.xRel, y: o.yRel, width: o.breiteM, height: o.hoeheM, depth: o.tiefeM, pitch: o.pitch },
        aRad, o.xRel * W, o.yRel * H, o.lyApexMax ?? Infinity,
      );
      const fpFlaeche = fp.length >= 3 ? polygonFlaecheM2(fp) : 0;
      const fpOk = fp.length >= 3
        && istSichereKonvexeFlaeche(fp, fp.length >= 4 ? 4 : 3)
        && fp.every((p) => punktInPolygon(p, poly))
        && rechteckKantenAbstandOk(fp, poly, KANTEN_RAND_M)
        && fpFlaeche > 1e-6 && fpFlaeche < bruttoFlaecheM2 * 0.9;
      if (fpOk) {
        polyFootprint = fp;
        oeffnungFlaecheM2 = fpFlaeche; // Netto nutzt die Polygonfläche (Shoelace)
      }
    }
    nettoFlaecheM2 = Math.max(0, bruttoFlaecheM2 - oeffnungFlaecheM2);
    grund = gaubeLochbar
      ? (polyFootprint
        ? 'Gaube auf sicherer Rechteckfläche (Sattel/Pult) — echtes Hauptdach-Loch als realer Fußabdruck-Polygon. Anschluss-/Kehllinien wie Gaubentyp; Dachdeckeranschluss fachlich prüfen.'
        : 'Gaube auf sicherer Rechteckfläche (Sattel/Pult) — echtes Hauptdach-Loch (Rechteck-Fußabdruck). Anschlusslinien schematisch.')
      : rechteckig
        ? 'Einfache Rechteckfläche + einfache Durchdringung — echter maßhaltiger Ausschnitt (Stufe B).'
        : konvex
          ? 'Sichere konvexe Dachfläche, Öffnung mit Randabstand vollständig innen — echtes Dachhaut-Loch.'
          : 'Öffnung vollständig in einem sicheren konvexen Teilbereich der L-/T-/U-Fläche — echtes Dachhaut-Loch.';
  } else {
    // Maßhaltig berechnet, aber Ausschnitt (noch) nicht sicher -> Prüffeld behalten.
    status = 'teilweise';
    grund = !sichereFlaeche
      ? 'Keine sichere Dachfläche (nicht konvex und nicht sicher in konvexe Teilbereiche zerlegbar) — Ausschnitt geplant, Prüffeld.'
      : (istGaube && !gaubeLochbar)
        ? 'Gaube nur auf rechteckiger, geneigter Sattel-/Pultfläche als echtes Loch — hier Prüffeld (z. B. Walm/Trapez/L-T-U/Flach).'
        : (!einfach && !istGaube)
          ? 'Komplexe Durchdringung — echter Hauptdach-Ausschnitt folgt später, Prüffeld.'
          : teilflaechen.length > 0
            ? 'Öffnung schneidet eine Innenkante/liegt im Innenhof oder einspringenden Winkel der L-/T-/U-Fläche — Prüffeld.'
            : 'Öffnung zu nah an einer (schrägen) Kante bzw. in einer schmalen Spitze — Prüffeld.';
    warnungen.push('Echter Dachflächen-Ausschnitt nur schematisch (Prüffeld). Materialfläche unverändert (Brutto).');
  }

  return {
    surfaceId, art: o.art, status, echterAusschnitt, innerhalb,
    rechteckM: { uMin: r3(uMin), uMax: r3(uMax), vMin: r3(vMin), vMax: r3(vMax) },
    eckenLokal,
    breiteM: rect.breiteM, hoeheM: rect.tiefeM, // tiefeM hier = v-Ausdehnung der Öffnung (Dachfenster->Länge, sonst Footprint)
    sicherheitsrandM: rect.sicherheitsrandM,
    oeffnungFlaecheM2: r3(oeffnungFlaecheM2),
    bruttoFlaecheM2: r3(bruttoFlaecheM2),
    nettoFlaecheM2: r3(nettoFlaecheM2),
    restPolygone,
    poly: polyFootprint,
    warnungen,
    grund,
  };
}

export interface FlaechenBilanz {
  bruttoM2: number;
  /** Summe der Öffnungen mit ECHTEM Ausschnitt (gehen in den Netto-Abzug). */
  oeffnungEchtM2: number;
  /** Summe der Öffnungen, die nur als Prüffeld geführt werden (KEIN Abzug). */
  oeffnungPrueffeldM2: number;
  nettoM2: number;
}

/**
 * Eingabeaufforderung 19: bestimmt für EINE Dachfläche die SICHEREN echten Löcher (THREE.Shape.holes)
 * aus mehreren Öffnungen. Echtes Loch nur, wenn berechneAusschnitt es als echterAusschnitt einstuft
 * (rechteckige Fläche + einfache Durchdringung + innerhalb) UND es sich nicht mit einem bereits
 * angenommenen Loch überlappt (sonst Rückfall auf Prüffeld). Netto = Brutto − Summe echter Löcher.
 */
export interface OeffnungMitId extends OeffnungEingabe { id: string; }
export interface AngenommenesLoch { id: string; rect: OeffnungRechteckM; poly?: Punkt2D[]; }
export interface LochSatz {
  surfaceId: string;
  rechteckigeFlaeche: boolean;
  loecher: AngenommenesLoch[];   // sichere, NICHT überlappende echte Löcher (u/v Meter)
  echteIds: string[];            // Öffnungen mit echtem Loch
  prueffeldIds: string[];        // Öffnungen, die nur Prüffeld bleiben
  bruttoM2: number; oeffnungEchtM2: number; oeffnungPrueffeldM2: number; nettoM2: number;
  warnungen: string[];
}

function rechteckeUeberlappen(a: OeffnungRechteckM, b: OeffnungRechteckM): boolean {
  return !(a.uMax <= b.uMin || a.uMin >= b.uMax || a.vMax <= b.vMin || a.vMin >= b.vMax);
}

export function sichereLoecher(
  surfaceId: string,
  polygon: ReadonlyArray<Punkt2D>,
  width: number,
  height: number,
  oeffnungen: ReadonlyArray<OeffnungMitId>,
  gaubeErlaubt = false, // E23: geneigte, rechteckige Sattel-/Pultfläche -> Gauben-Löcher zulässig
  aRad = 0,             // E25: Dachneigung (für realen Gauben-Fußabdruck als Polygon)
): LochSatz {
  const poly = (polygon && polygon.length >= 3) ? polygon.map((p) => ({ x: endlich(p.x), y: endlich(p.y) })) : [];
  const bruttoM2 = poly.length >= 3 ? Math.max(0, polygonFlaecheM2(poly)) : Math.max(0.01, endlich(width)) * Math.max(0.01, endlich(height));
  const rechteckigeFlaeche = istAchsenRechteck(poly);
  const loecher: AngenommenesLoch[] = [];
  const echteIds: string[] = [];
  const prueffeldIds: string[] = [];
  const warnungen: string[] = [];
  let echt = 0, pruef = 0;

  for (const o of (oeffnungen || [])) {
    const b = berechneAusschnitt(surfaceId, poly, width, height, o, 0.1, gaubeErlaubt, aRad);
    if (b.echterAusschnitt && rechteckeNichtUeberlappend(loecher, b.rechteckM)) {
      loecher.push({ id: o.id, rect: b.rechteckM, poly: b.poly }); // E25: poly (Pentagon) wenn vorhanden
      echteIds.push(o.id);
      echt += Math.max(0, b.oeffnungFlaecheM2); // E25: bei Polygon-Loch = Polygonfläche (Shoelace)
    } else {
      prueffeldIds.push(o.id);
      pruef += Math.max(0, b.oeffnungFlaecheM2);
      if (b.echterAusschnitt) warnungen.push(`Öffnung „${o.id}" überlappt ein anderes Loch — Rückfall auf Prüffeld.`);
    }
  }
  return {
    surfaceId, rechteckigeFlaeche, loecher, echteIds, prueffeldIds,
    bruttoM2: r3(bruttoM2), oeffnungEchtM2: r3(echt), oeffnungPrueffeldM2: r3(pruef),
    nettoM2: r3(Math.max(0, bruttoM2 - echt)), warnungen,
  };
}

function rechteckeNichtUeberlappend(vorhanden: AngenommenesLoch[], neu: OeffnungRechteckM): boolean {
  return !vorhanden.some((l) => rechteckeUeberlappen(l.rect, neu));
}

/** Aggregiert mehrere Befunde zu einer Flächenbilanz (Brutto unverändert; Netto nur echte Ausschnitte). */
export function flaechenBilanz(bruttoM2: number, befunde: ReadonlyArray<AusschnittBefund>): FlaechenBilanz {
  let echt = 0, pruef = 0;
  for (const b of befunde) {
    if (b.echterAusschnitt) echt += Math.max(0, b.oeffnungFlaecheM2);
    else pruef += Math.max(0, b.oeffnungFlaecheM2);
  }
  const brutto = Math.max(0, endlich(bruttoM2));
  return { bruttoM2: r3(brutto), oeffnungEchtM2: r3(echt), oeffnungPrueffeldM2: r3(pruef), nettoM2: r3(Math.max(0, brutto - echt)) };
}

export type { Punkt2D };
