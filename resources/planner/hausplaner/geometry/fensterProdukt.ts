/**
 * Hausplaner — Fenster-/Tür-Produktkern (Solar Aspekt Fensterbau). Reine, getestete Fachlogik:
 *   - Uw-Berechnung nach DIN EN ISO 10077-1 (Rahmen + Glas + Randverbund),
 *   - Einbruchwiderstand RC nach EN 1627 (Machbarkeit je Verglasung),
 *   - Positionspreis aus Komponenten (Profil je Umfang, Glas je Fläche, Beschlag, RC-Aufpreis).
 *
 * WICHTIG: Der Katalog unten enthält BEISPIELWERTE (typische Größenordnungen), NICHT die echten
 * Schüco-/Lieferanten-Daten. Uf/Ug/Ψ/Preise ersetzt Yama durch die realen Werte — das ist Daten-,
 * kein Code-Wechsel. Die NORMFORMELN sind korrekt und bleiben.
 *
 * Kein three/Konva, keine Szene-Mutation.
 */

export type RahmenMaterial = 'kunststoff' | 'aluminium' | 'holz' | 'holz-alu';
export type RcKlasse = 'ohne' | 'RC1N' | 'RC2N' | 'RC2' | 'RC3';
export type OeffnungsArt = 'fest' | 'dreh' | 'kipp' | 'dreh-kipp';

/** Rangordnung der RC-Klassen (für Machbarkeitsvergleich). */
const RC_RANG: Record<RcKlasse, number> = { ohne: 0, RC1N: 1, RC2N: 2, RC2: 3, RC3: 4 };

export interface ProfilSystem {
  id: string;
  name: string;
  material: RahmenMaterial;
  /** Rahmen-Wärmedurchgang Uf, W/(m²·K) — BEISPIEL. */
  uf: number;
  /** Rahmen-Ansichtsbreite (sichtbar), mm — bestimmt Rahmen-/Glasfläche. BEISPIEL. */
  ansichtsbreiteMm: number;
  /** Bautiefe, mm — informativ. */
  bautiefeMm: number;
  /** Rahmenpreis je laufendem Meter Umfang, € — BEISPIEL. */
  preisProMeter: number;
}

export interface Verglasung {
  id: string;
  name: string;
  scheiben: 2 | 3;
  /** Glas-Wärmedurchgang Ug, W/(m²·K) — BEISPIEL. */
  ug: number;
  /** Gesamtenergiedurchlassgrad g, 0..1 — BEISPIEL. */
  gWert: number;
  /** längenbez. Wärmedurchgang Randverbund Ψg, W/(m·K) — BEISPIEL (warme Kante ~0,04). */
  psiRandverbund: number;
  /** Glaspreis je m², € — BEISPIEL. */
  preisProM2: number;
  /** höchste erreichbare RC-Klasse mit dieser Verglasung (EN 1627/EN 356). */
  rcTauglichBis: RcKlasse;
}

/** BEISPIEL-Katalog — echte Werte trägt Yama nach. */
export const PROFIL_KATALOG: ReadonlyArray<ProfilSystem> = [
  { id: 'kunststoff-70', name: 'Kunststoff 70 mm (5-Kammer)', material: 'kunststoff', uf: 1.3, ansichtsbreiteMm: 117, bautiefeMm: 70, preisProMeter: 38 },
  { id: 'kunststoff-82', name: 'Kunststoff 82 mm (7-Kammer)', material: 'kunststoff', uf: 1.0, ansichtsbreiteMm: 120, bautiefeMm: 82, preisProMeter: 52 },
  { id: 'alu-75', name: 'Aluminium 75 mm (thermisch getrennt)', material: 'aluminium', uf: 1.6, ansichtsbreiteMm: 110, bautiefeMm: 75, preisProMeter: 72 },
  { id: 'holz-alu-90', name: 'Holz-Alu 90 mm', material: 'holz-alu', uf: 1.1, ansichtsbreiteMm: 105, bautiefeMm: 90, preisProMeter: 95 },
];

export const VERGLASUNG_KATALOG: ReadonlyArray<Verglasung> = [
  { id: '2fach-standard', name: '2-fach Wärmeschutz', scheiben: 2, ug: 1.1, gWert: 0.6, psiRandverbund: 0.04, preisProM2: 60, rcTauglichBis: 'RC1N' },
  { id: '3fach-standard', name: '3-fach Wärmeschutz', scheiben: 3, ug: 0.6, gWert: 0.5, psiRandverbund: 0.04, preisProM2: 95, rcTauglichBis: 'RC2N' },
  { id: '3fach-p4a', name: '3-fach + P4A (durchwurfhemmend)', scheiben: 3, ug: 0.6, gWert: 0.5, psiRandverbund: 0.04, preisProM2: 160, rcTauglichBis: 'RC2' },
  { id: '3fach-p5a', name: '3-fach + P5A (durchbruchhemmend)', scheiben: 3, ug: 0.6, gWert: 0.5, psiRandverbund: 0.04, preisProM2: 240, rcTauglichBis: 'RC3' },
];

/** Beschlag-Pauschale je Öffnungsart, € — BEISPIEL. */
const BESCHLAG_PREIS: Record<OeffnungsArt, number> = { fest: 0, dreh: 45, kipp: 45, 'dreh-kipp': 85 };
/** RC-Aufpreis (Beschlag/Pilzzapfen/Verriegelung), € — BEISPIEL. */
const RC_AUFPREIS: Record<RcKlasse, number> = { ohne: 0, RC1N: 30, RC2N: 90, RC2: 140, RC3: 260 };

export interface UwEingabe {
  breiteMm: number;
  hoeheMm: number;
  uf: number;
  ug: number;
  /** Rahmen-Ansichtsbreite, mm. */
  ansichtsbreiteMm: number;
  /** Ψg Randverbund, W/(m·K). Default 0,04 (warme Kante). */
  psiRandverbund?: number;
}

export interface UwErgebnis {
  uw: number;        // W/(m²·K), auf 0,01 gerundet
  agM2: number;      // Glasfläche
  afM2: number;      // Rahmenfläche
  lgM: number;       // sichtbarer Glasumfang
}

/**
 * Uw nach DIN EN ISO 10077-1:  Uw = (Ag·Ug + Af·Uf + lg·Ψg) / (Ag + Af).
 * Vereinfachtes Ein-Feld-Modell (umlaufender Rahmen der Ansichtsbreite; ein Glasfeld) — für Sprossen/
 * Mehrflügel später verfeinerbar. Bei entarteten Maßen (Rahmen ≥ halbe Kante) → Ag=0.
 */
export function berechneUw(e: UwEingabe): UwErgebnis {
  const B = e.breiteMm / 1000;
  const H = e.hoeheMm / 1000;
  const rf = e.ansichtsbreiteMm / 1000;
  const glasB = Math.max(0, B - 2 * rf);
  const glasH = Math.max(0, H - 2 * rf);
  const ag = glasB * glasH;
  const gesamt = B * H;
  const af = Math.max(0, gesamt - ag);
  const lg = ag > 0 ? 2 * (glasB + glasH) : 0;
  const psi = e.psiRandverbund ?? 0.04;
  const nenner = ag + af;
  const uw = nenner > 0 ? (ag * e.ug + af * e.uf + lg * psi) / nenner : e.uf;
  return {
    uw: Math.round(uw * 100) / 100,
    agM2: Math.round(ag * 1000) / 1000,
    afM2: Math.round(af * 1000) / 1000,
    lgM: Math.round(lg * 1000) / 1000,
  };
}

/** Ist die gewünschte RC-Klasse mit dieser Verglasung erreichbar (EN 1627)? */
export function rcMachbar(rc: RcKlasse, verglasung: Verglasung): boolean {
  return RC_RANG[rc] <= RC_RANG[verglasung.rcTauglichBis];
}

export interface PreisEingabe {
  breiteMm: number;
  hoeheMm: number;
  profil: ProfilSystem;
  verglasung: Verglasung;
  oeffnungsArt: OeffnungsArt;
  rc: RcKlasse;
}

export interface PreisErgebnis {
  rahmen: number;
  glas: number;
  beschlag: number;
  rcAufpreis: number;
  gesamt: number;   // € netto, auf 1 € gerundet
}

/** Positionspreis aus Komponenten (transparent, alle Sätze BEISPIEL). */
export function preisFenster(e: PreisEingabe): PreisErgebnis {
  const B = e.breiteMm / 1000;
  const H = e.hoeheMm / 1000;
  const umfang = 2 * (B + H);
  const uw = berechneUw({ breiteMm: e.breiteMm, hoeheMm: e.hoeheMm, uf: e.profil.uf, ug: e.verglasung.ug, ansichtsbreiteMm: e.profil.ansichtsbreiteMm });
  const rahmen = umfang * e.profil.preisProMeter;
  const glas = uw.agM2 * e.verglasung.preisProM2;
  const beschlag = BESCHLAG_PREIS[e.oeffnungsArt];
  const rcAufpreis = RC_AUFPREIS[e.rc];
  const gesamt = rahmen + glas + beschlag + rcAufpreis;
  const r = (x: number): number => Math.round(x);
  return { rahmen: r(rahmen), glas: r(glas), beschlag: r(beschlag), rcAufpreis: r(rcAufpreis), gesamt: r(gesamt) };
}

export const profilNach = (id: string): ProfilSystem | undefined => PROFIL_KATALOG.find((p) => p.id === id);
export const verglasungNach = (id: string): Verglasung | undefined => VERGLASUNG_KATALOG.find((v) => v.id === id);
