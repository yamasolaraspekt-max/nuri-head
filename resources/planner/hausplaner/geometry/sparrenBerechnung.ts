/**
 * Hausplaner — Dachstuhl/Sparren-Vorbemessung (Einfeld-Sparren) nach Eurocode.
 *
 * Reine, getestete Fachlogik (Solar Aspekt), Muster wie berechneTreppe. Referenz für Umfang/Ein-
 * und Ausgaben: kalk.pro (Sparrenrechner) — NICHTS kopiert; die Formeln stammen aus den öffentlichen
 * Normen:
 *   - Schneelast: DIN EN 1991-1-3 + Nationaler Anhang (charakt. Bodenschneelast sk je Zone/Höhe,
 *     Formbeiwert μ1 nach Dachneigung).
 *   - Holzbemessung: DIN EN 1995-1-1 (Eurocode 5): Biegenachweis σ_m,d ≤ f_m,d und Durchbiegung.
 *
 * WICHTIG: VORBEMESSUNG (Einfeldträger, gleichmäßige Last, senkrechte Lastkomponente). Ersetzt KEINE
 * prüffähige Statik — dient der schnellen Querschnittsabschätzung im Verkauf/Planung. Wind, Mehrfeld,
 * Knicken, Auflagerpressung, Lastkombinationen bleiben dem Tragwerksplaner. Keine three/Konva.
 */

export type Schneezone = 1 | 2 | 3;
export type Holzklasse = 'C24' | 'C30';

interface HolzKennwert { fmk: number; e0mean: number } // N/mm²
const HOLZ: Record<Holzklasse, HolzKennwert> = {
  C24: { fmk: 24, e0mean: 11000 },
  C30: { fmk: 30, e0mean: 12000 },
};

// Teilsicherheits-/Modifikationsbeiwerte (Vollholz, NKL 1/2, mittelfristige Einwirkung Schnee).
const GAMMA_G = 1.35;
const GAMMA_Q = 1.5;
const GAMMA_M = 1.3;
const KMOD = 0.9;
const DURCHBIEGUNG_GRENZE = 300; // L/300 (Empfehlung Endzustand, Vorbemessung)

/** Charakteristische Bodenschneelast sk (kN/m²) nach DIN EN 1991-1-3/NA, Zone + Geländehöhe A (m). */
export function bodenschneelast(zone: Schneezone, gelaendehoeheM: number): number {
  const A = Math.max(0, gelaendehoeheM);
  const t = (A + 140) / 760;
  const roh =
    zone === 1 ? 0.19 + 0.91 * t * t :
    zone === 2 ? 0.25 + 1.91 * t * t :
                 0.31 + 2.91 * t * t;
  const min = zone === 1 ? 0.65 : zone === 2 ? 0.85 : 1.10;
  return Math.max(min, roh);
}

/** Formbeiwert μ1 (Pult-/Satteldach) nach Dachneigung α (Grad): 0,8 bis 30°, linear auf 0 bei 60°. */
export function formbeiwertSchnee(neigungGrad: number): number {
  const a = Math.max(0, neigungGrad);
  if (a <= 30) return 0.8;
  if (a >= 60) return 0;
  return 0.8 * (60 - a) / 30;
}

export interface SparrenEingabe {
  /** Gebäudebreite (Spannweite Traufe–Traufe), m. Sparren-Horizontalspanne = Breite/2 (Satteldach). */
  gebaeudebreiteM: number;
  neigungGrad: number;
  /** Sparrenabstand (Achsabstand), m. */
  sparrenabstandM: number;
  /** Querschnitt Sparren, mm. */
  breiteMm: number;
  hoeheMm: number;
  schneezone: Schneezone;
  gelaendehoeheM: number;
  /** ständige Last (Dachdeckung + Lattung + Sparren-Eigengewicht) auf Dachfläche, kN/m². Default 0,9. */
  eigenlastKnM2?: number;
  holzklasse?: Holzklasse;
}

export interface SparrenErgebnis {
  sparrenlaengeM: number;
  bodenschneelastKnM2: number;   // sk
  schneelastKnM2: number;        // s = μ1·sk (Horizontalprojektion)
  designLinienlastPerpKnM: number; // w_perp,d entlang Sparren
  momentKnM: number;
  sigmaMpa: number;              // σ_m,d
  biegefestigkeitMpa: number;    // f_m,d
  ausnutzungBiegung: number;     // σ_m,d / f_m,d
  durchbiegungMm: number;
  durchbiegungGrenzeMm: number;  // L/300
  ausnutzungDurchbiegung: number;
  bestanden: boolean;            // beide Nachweise ≤ 1,0
  /**
   * N-003-Vorbehalt — **Pflichtfeld, kein Kommentar.** Jede ausgegebene Bemessungszahl traegt
   * ihre Grenze MIT: wer die Zahl sieht, sieht den Satz. Er steht hier und nicht in der Anzeige,
   * weil es heute genau EINEN Ausgabeweg gibt und morgen Export, Stueckliste und PDF — wer ihn in
   * die Anzeige schreibt, pflegt ihn danach an vier Stellen und die vierte vergisst ihn.
   *
   * Als Feld kann keine Ausgabestelle ihn weglassen, ohne ihn AKTIV zu unterdruecken. Das faellt auf.
   */
  vorbehalt: string;
}

/**
 * Der Vorbehaltssatz im Wortlaut, wie Yama ihn am 12.08.2026 festgelegt hat. Eine Fassung, eine
 * Stelle — zwei Fassungen derselben Warnung waeren eine zweite Wahrheit.
 *
 * N-003 ist DAUERGELB: nicht wegen der Rechenqualitaet, sondern weil der Geltungsbereich fachlich
 * unvollstaendig ist und bleibt (Wind, Mehrfeld, Knicken fehlen). Das ist kein Mangel, den man
 * wegarbeitet — das ist die Natur einer Vorbemessung.
 */
export const N003_VORBEHALT = 'Vorbemessung, ersetzt keine prüffähige Statik';

const rad = (g: number): number => (g * Math.PI) / 180;
const r = (x: number, n = 2): number => { const f = 10 ** n; return Math.round(x * f) / f; };

export function berechneSparren(e: SparrenEingabe): SparrenErgebnis {
  const holz = HOLZ[e.holzklasse ?? 'C24'];
  const gk = e.eigenlastKnM2 ?? 0.9;
  const a = rad(e.neigungGrad);
  const cosA = Math.cos(a);

  const lhMm = (e.gebaeudebreiteM / 2) * 1000;         // Horizontalspanne
  const lsMm = cosA > 0 ? lhMm / cosA : lhMm;          // Sparrenlänge (geneigt)

  const sk = bodenschneelast(e.schneezone, e.gelaendehoeheM);
  const s = formbeiwertSchnee(e.neigungGrad) * sk;      // kN/m² Horizontalprojektion
  const sSloped = s * cosA;                             // auf Dachfläche umgerechnet

  // Flächenlasten auf Dachfläche → Linienlast entlang Sparren (·Sparrenabstand),
  // nur die zum Sparren senkrechte Komponente erzeugt Biegung (·cosα).
  const wDesign = (GAMMA_G * gk + GAMMA_Q * sSloped) * e.sparrenabstandM; // kN/m entlang Sparren
  const wPerp = wDesign * cosA;                          // kN/m = N/mm
  const wCharPerp = (gk + sSloped) * e.sparrenabstandM * cosA;

  const M = (wPerp * lsMm * lsMm) / 8;                   // N·mm  (1 kN/m = 1 N/mm)
  const W = (e.breiteMm * e.hoeheMm * e.hoeheMm) / 6;    // mm³
  const sigma = W > 0 ? M / W : Infinity;               // N/mm²
  const fmd = (KMOD * holz.fmk) / GAMMA_M;              // N/mm²

  const I = (e.breiteMm * e.hoeheMm ** 3) / 12;         // mm⁴
  const delta = I > 0 ? (5 * wCharPerp * lsMm ** 4) / (384 * holz.e0mean * I) : Infinity;
  const deltaGrenze = lsMm / DURCHBIEGUNG_GRENZE;

  const ausnBiegung = sigma / fmd;
  const ausnDurch = delta / deltaGrenze;

  return {
    sparrenlaengeM: r(lsMm / 1000, 3),
    bodenschneelastKnM2: r(sk),
    schneelastKnM2: r(s),
    designLinienlastPerpKnM: r(wPerp, 3),
    momentKnM: r(M / 1e6, 2),
    sigmaMpa: r(sigma),
    biegefestigkeitMpa: r(fmd),
    ausnutzungBiegung: r(ausnBiegung),
    durchbiegungMm: r(delta, 1),
    durchbiegungGrenzeMm: r(deltaGrenze, 1),
    ausnutzungDurchbiegung: r(ausnDurch),
    bestanden: ausnBiegung <= 1 && ausnDurch <= 1,
    vorbehalt: N003_VORBEHALT,
  };
}
