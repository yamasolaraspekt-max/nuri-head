/**
 * Eingabeaufforderung 11: reine, testbare Hilfslogik zur FLÄCHENABHÄNGIGEN Platzierung von
 * Standard-Aufbauten (Kamin/Dachfenster/Lüfter/Sat/Gaube/Lichtkuppel) auf einer Dachfläche.
 *
 * Die Aufbau-Wünsche (relative Soll-Position + Soll-Maße aus der Vorlage) werden gegen die
 * ECHTE Dachflächengeometrie geprüft und korrigiert:
 *  - Maße werden an die lokal verfügbare Breite/Höhe gekoppelt (kein zu breiter Aufbau auf
 *    schmaler/trapezförmiger Walmfläche),
 *  - Positionen werden in die Fläche hineingezogen (Mindestabstand zu First/Traufe/Ortgang),
 *  - Trapez-/Dreieckflächen verjüngen sich zum First -> verfügbare Breite ist y-abhängig,
 *  - einfache Kollisionsprüfung zwischen den Aufbauten (AABB in u/v-Metern),
 *  - bei zu kleiner Fläche oder unlösbarer Lage -> pruefpflichtig (statt falsch zu setzen).
 *
 * Koordinaten: u = parallel Traufe (Bounding-Box-Breite surf.width), v = Traufe->First
 * (surf.height). Relativwerte x/y ∈ (0,1) beziehen sich auf diese Bounding-Box (so interpretiert
 * die Engine die Obstacle-Position). Die Fläche ist horizontal um u = breiteTraufeM/2 zentriert.
 *
 * Rein (keine React-/THREE-Abhängigkeit). KEINE Dacheindeckung/Material.
 */

export type AufbauArt =
  | 'chimney' | 'window' | 'vent' | 'sat' | 'lichtkuppel'
  | 'schleppgaube' | 'trapezgaube' | 'flachgaube' | 'giebelgaube';

export type DachflaecheForm = 'rechteck' | 'trapez' | 'dreieck';

export interface DachflaecheInfo {
  surfaceId: string;
  /** u-Breite an der Traufe (= surf.width Bounding-Box), Meter. */
  breiteTraufeM: number;
  /** u-Breite am First; == breiteTraufeM bei Rechteck; ~0 bei Dreieck (Trapez verjüngt). */
  breiteFirstM: number;
  /** geneigte Höhe Traufe->First (= surf.height), Meter. */
  hoeheM: number;
  form: DachflaecheForm;
}

export interface AufbauWunsch {
  art: AufbauArt;
  surfaceId: string;
  xRel: number; yRel: number;
  breiteM: number; hoeheM: number; tiefeM: number; pitchGrad: number;
}

export interface PlatzierterAufbau extends AufbauWunsch {
  /** true, wenn der Aufbau nicht sicher/kollisionsfrei in die Fläche passt (best effort gesetzt). */
  pruefpflichtig: boolean;
  hinweis?: string;
  /** true, wenn Größe/Position an die reale Dachfläche angepasst wurden. */
  angepasst: boolean;
}

// Richtwerte (konservativ)
export const RAND_M = 0.4;            // Mindestabstand zu First/Traufe/Ortgang
export const MAX_BREITE_ANTEIL = 0.5; // max Anteil der lokal verfügbaren Breite
export const MAX_HOEHE_ANTEIL = 0.5;  // max Anteil der geneigten Flächenhöhe
export const MIN_AUFBAU_M = 0.3;      // darunter -> prüfpflichtig (zu klein)
export const MIN_GAUBE_M = 1.2;       // Gaube darunter unsinnig -> prüfpflichtig
export const AUFBAU_ABSTAND_M = 0.4;  // Mindestabstand zwischen Aufbauten

function endlich(n: unknown, fallback = 0): number {
  return typeof n === 'number' && Number.isFinite(n) ? n : fallback;
}
function clamp(v: number, lo: number, hi: number): number {
  return Math.min(hi, Math.max(lo, v));
}
function istGaube(art: AufbauArt): boolean {
  return art === 'schleppgaube' || art === 'trapezgaube' || art === 'flachgaube' || art === 'giebelgaube';
}

/** Lokal verfügbare Breite (u) an einer relativen v-Position (Trapez linear interpoliert). */
export function verfuegbareBreiteM(f: DachflaecheInfo, yRel: number): number {
  const y = clamp(endlich(yRel), 0, 1);
  return Math.max(0, endlich(f.breiteTraufeM) * (1 - y) + endlich(f.breiteFirstM) * y);
}

interface AABB { u0: number; u1: number; v0: number; v1: number; }
function boxOf(uM: number, vM: number, breiteM: number, hoeheM: number): AABB {
  const hu = breiteM / 2 + AUFBAU_ABSTAND_M / 2;
  const hv = hoeheM / 2 + AUFBAU_ABSTAND_M / 2;
  return { u0: uM - hu, u1: uM + hu, v0: vM - hv, v1: vM + hv };
}
function ueberlappt(a: AABB, b: AABB): boolean {
  return !(a.u1 <= b.u0 || a.u0 >= b.u1 || a.v1 <= b.v0 || a.v0 >= b.v1);
}

/**
 * Platziert die Aufbau-Wünsche flächenabhängig + kollisionsarm. Reihenfolge = Priorität.
 * `bestehend` = bereits auf DERSELBEN Fläche vorhandene Aufbauten (für Kollisionsprüfung).
 */
export function platziereAufbauten(
  wuensche: AufbauWunsch[],
  f: DachflaecheInfo,
  bestehend: AufbauWunsch[] = [],
): PlatzierterAufbau[] {
  const W = endlich(f.breiteTraufeM);
  const H = endlich(f.hoeheM);
  const out: PlatzierterAufbau[] = [];

  if (W <= 0 || H <= 0) {
    return wuensche.map((w) => ({
      ...w, pruefpflichtig: true, angepasst: false,
      hinweis: 'Dachfläche unbekannt/zu klein — Aufbau bitte prüfen.',
    }));
  }

  // Belegte AABBs (bestehende Aufbauten auf gleicher Fläche zählen mit)
  const belegt: AABB[] = bestehend
    .filter((b) => b.surfaceId === f.surfaceId)
    .map((b) => boxOf(clamp(endlich(b.xRel), 0, 1) * W, clamp(endlich(b.yRel), 0, 1) * H, endlich(b.breiteM), b.art === 'window' ? endlich(b.hoeheM) : endlich(b.tiefeM)));

  for (const w of wuensche) {
    const hinweise: string[] = [];
    let pruef = false;
    let angepasst = false;

    // (1) SLOPE-TIEFE (v-Footprint entlang der Dachschräge) an die Fläche koppeln.
    // WICHTIG: Die Ausdehnung entlang Traufe->First ist art-abhängig:
    //  - Dachfenster ('window'): hoeheM ist die Länge entlang der Schräge.
    //  - Gaube/Kamin/Sat/Lichtkuppel/Lüfter: tiefeM (depth) ist die Ausdehnung entlang der Schräge;
    //    hoeheM ist hier die VERTIKALE Höhe (steht über der Fläche) und gehört NICHT zum v-Footprint.
    // Bisheriger Fehler: für Gauben wurde hoeheM (1,5) statt tiefeM (2,5) verwendet -> der First-
    // Randabstand war zu klein -> Gauben ragten über den First und schwebten über der anderen Seite.
    const istWindow = w.art === 'window';
    const vExtent0 = istWindow ? endlich(w.hoeheM) : endlich(w.tiefeM);
    const vFoot = Math.min(Math.max(MIN_AUFBAU_M, vExtent0), MAX_HOEHE_ANTEIL * H);
    if (vFoot < vExtent0 - 1e-9) angepasst = true;
    const halbV = vFoot / 2;
    const vMin = RAND_M + halbV;
    const vMax = H - RAND_M - halbV;
    let vM = clamp(endlich(w.yRel), 0, 1) * H;
    if (vMax < vMin) { pruef = true; hinweise.push('Fläche zu niedrig für diesen Aufbau'); vM = H / 2; }
    else { const c = clamp(vM, vMin, vMax); if (Math.abs(c - vM) > 1e-9) angepasst = true; vM = c; }
    let yRel = clamp(vM / H, 0.02, 0.98);

    // (2) Breite (u) an die LOKAL verfügbare Breite an dieser y-Position koppeln
    const availW = verfuegbareBreiteM(f, yRel);
    const breiteU0 = endlich(w.breiteM);
    let breiteU = Math.min(breiteU0, MAX_BREITE_ANTEIL * availW);
    if (breiteU < breiteU0 - 1e-9) angepasst = true;
    const minBreite = istGaube(w.art) ? MIN_GAUBE_M : MIN_AUFBAU_M;
    if (breiteU < minBreite) {
      pruef = true; hinweise.push('Dachfläche zu schmal für diesen Aufbau');
      breiteU = Math.max(MIN_AUFBAU_M, Math.min(breiteU, availW * 0.8));
    }

    // (3) u-Position in die (zentrierte) Fläche ziehen
    const halbU = breiteU / 2;
    const uCenter = W / 2;
    const uMin = uCenter - availW / 2 + RAND_M + halbU;
    const uMax = uCenter + availW / 2 - RAND_M - halbU;
    let uM = clamp(endlich(w.xRel), 0, 1) * W;
    if (uMax < uMin) { pruef = true; hinweise.push('Fläche zu schmal'); uM = uCenter; }
    else { const c = clamp(uM, uMin, uMax); if (Math.abs(c - uM) > 1e-9) angepasst = true; uM = c; }

    // (4) einfache Kollisionsvermeidung (entlang u verschieben, sonst Zeile tiefer) — v-Footprint = vFoot
    let versuche = 0;
    while (belegt.some((b) => ueberlappt(boxOf(uM, vM, breiteU, vFoot), b)) && versuche < 10) {
      angepasst = true;
      uM += breiteU + AUFBAU_ABSTAND_M;
      if (uM > uMax) {
        uM = Math.max(uMin, uCenter - availW / 2 + RAND_M + halbU);
        vM = clamp(vM + vFoot + AUFBAU_ABSTAND_M, vMin, vMax);
      }
      versuche++;
    }
    if (belegt.some((b) => ueberlappt(boxOf(uM, vM, breiteU, vFoot), b))) {
      pruef = true; hinweise.push('keine kollisionsfreie Lage gefunden');
    }

    yRel = clamp(vM / H, 0.02, 0.98);
    const xRel = clamp(uM / W, 0.02, 0.98);
    belegt.push(boxOf(uM, vM, breiteU, vFoot));

    // Ausgabe: hoeheM (vertikale Höhe) bleibt bei Gaube/Kamin erhalten; nur die SLOPE-Ausdehnung
    // wird flächenabhängig geklemmt (window -> hoeheM, sonst -> tiefeM).
    out.push({
      art: w.art, surfaceId: f.surfaceId,
      xRel, yRel,
      breiteM: Math.max(MIN_AUFBAU_M, breiteU),
      hoeheM: istWindow ? Math.max(MIN_AUFBAU_M, vFoot) : Math.max(MIN_AUFBAU_M, endlich(w.hoeheM)),
      tiefeM: istWindow ? Math.max(0.05, endlich(w.tiefeM)) : Math.max(0.05, vFoot),
      pitchGrad: Math.max(0, endlich(w.pitchGrad)),
      pruefpflichtig: pruef, angepasst,
      hinweis: hinweise.length ? hinweise.join('; ') : undefined,
    });
  }

  return out;
}
