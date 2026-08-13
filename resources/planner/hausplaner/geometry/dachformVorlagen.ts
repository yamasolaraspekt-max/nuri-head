/**
 * Dachform-Vorlagenbibliothek — reine Fachdaten + Berechnungs-/Validierungslogik
 * für den 3D-Dach-&-PV-Planer (DachplanerProPage).
 *
 * Zweck: fachlich saubere Vorlagen (Geometrie / Zimmerer-Tragwerk / Dachdecker /
 * PV) als RICHTWERTE bereitstellen und — NUR wenn die vorhandene Geometrie-Engine
 * die Form WIRKLICH sauber baut — als 'verfuegbar' anwendbar machen. Alle übrigen
 * Formen sind 'geplant' (sichtbar, aber nicht anwendbar) statt als Platzhalter
 * still falsche Geometrie zu erzeugen.
 *
 * WICHTIG (Reinheit): Diese Datei ist bewusst rein — KEIN React/THREE-Import,
 * kein DOM-/Engine-/State-Zugriff. Sie gibt nur Daten zurück. Das eigentliche
 * Anwenden läuft in der Komponente ausschließlich über die vorhandenen Setter
 * setBuild/setCover, damit die bestehenden Reparatur-2/3-Warnungen feuern.
 *
 * Wiederverwendung bestehender, getesteter Reparaturmodule:
 *  - Reparatur 6: polygonFlaecheM2 (Shoelace) = echte geneigte Dachfläche.
 *  - Reparatur 1: sichererCos (Schutz cos→0), cmZuMFloor + DACH_FLOOR_CM (Floor).
 *
 * Fachregeln (verbindlich):
 *  - Traufhöhe = Auflager-/Wandlinie, NICHT die äußere Traufkante.
 *  - Winkel-Eingaben in Grad, alle Trigonometrie intern in Bogenmaß.
 *  - Waagerechter Traufüberstand → /cos(alpha); Ortgang (First-/Traufrichtung)
 *    NIE /cos; bereits geneigt gemessene Längen NICHT nochmals /cos.
 *  - Fläche aus Polygon (Shoelace) bzw. Grundrissprojektion /cos — nie width*height
 *    als echte Fläche bei nicht-rechteckigen Flächen.
 *  - Walm: Rücksprung = h/tan(alpha_walm); Firstlänge = L − 2·Rücksprung (darf <0
 *    werden = Inkonsistenz-Signal, NICHT still klemmen).
 *  - Norm-/Herstellerwerte (RDN/Deckmaß) sind konfigurierbare RICHTWERTE.
 *  - Keine vorgetäuschte Statik — nur Plausibilitäts-Hinweise; Bemessung separat.
 */

import { polygonFlaecheM2, type Punkt2D } from './polygonFlaeche';
import { sichererCos, cmZuMFloor, DACH_FLOOR_CM } from './dachWerte';
import { platziereAufbauten, type AufbauArt, type DachflaecheInfo, type PlatzierterAufbau } from './aufbauPlatzierung';
import { platziereSchneefang, SCHNEEFANG_HINWEIS, type DachLinienBauteil } from './linienBauteile';

export type { Punkt2D };
export type { PlatzierterAufbau, DachflaecheInfo, DachLinienBauteil };
export { SCHNEEFANG_HINWEIS };

// =====================================================================
// 1. TYPEN
// =====================================================================

export type VorlagenStatus = 'verfuegbar' | 'geplant';
export type RoofCategory = 'pitched' | 'flat'; // gespiegelt aus Engine
/** = RoofShape der Engine (DachplanerProPage). */
export type EngineRoofShape = 'sattel' | 'pult' | 'walm' | 'rect' | 'l-shape' | 't-shape' | 'u-shape';
export type RoofCovering = 'ziegel' | 'schiefer' | 'trapezblech' | 'bitumen' | 'kunststoff' | 'gruendach' | 'kies';
export type VorlagenShapeKey =
  | EngineRoofShape
  | 'zeltdach' | 'krueppelwalm' | 'mansard' | 'mansardwalm'
  | 'schleppdach' | 'versetztes-pult' | 'schmetterling' | 'grabendach'
  | 'sheddach' | 'tonnendach' | 'bogendach'
  | 'u-grundriss' | 'mehrfluegel' | 'halle'
  // Additive Erweiterung der Bibliothek (2026-06): weitere Sonderformen/Grundrisse.
  | 'pyramidendach' | 'mehrkoerper';

export interface DachformVorlage {
  id: string;
  name: string;
  kurzbeschreibung: string;
  status: VorlagenStatus;
  anwendbar: boolean; // anwendbar === (status === 'verfuegbar')
  schlagworte: string[];
  geplantGrund?: string; // nur bei status === 'geplant'
  geometrie: VorlagenGeometrie;
  zimmerer: VorlagenZimmerer;
  dachdecker: VorlagenDachdecker;
  pv: VorlagenPv;
  apply?: VorlagenApply; // undefined bei 'geplant'
  hinweisStatik: string; // Pflichttext, immer gesetzt
}

export interface VorlagenGeometrie {
  category: RoofCategory;
  engineShape: EngineRoofShape | null;
  shapeKey: VorlagenShapeKey;
  defaultLength: number; // m
  defaultWidth: number; // m
  defaultHeight: number; // m — Traufhöhe an Auflagerlinie!
  defaultPitch: number; // Grad
  defaultOverhang: number; // m, waagerechter Traufüberstand
  defaultOverhangGable: number; // m, Ortgang
  gefaelleRichtung?: 'breite' | 'laenge'; // nur Pult (Engine fix 'breite')
  symmetrie?: 'symmetrisch' | 'asymmetrisch';
  benoetigtLaengerGleichBreite?: boolean; // Walm: L>W Pflicht (sonst Firstlänge<=0)
}

export interface ZimmererFlags {
  sparren: boolean; firstpfette: boolean; mittelpfette: boolean; fusspfette: boolean;
  kehlbalken: boolean; stuhlsaeule: boolean; strebeKopfband: boolean; zange: boolean;
  aufschiebling: boolean; gratsparren: boolean; kehlsparren: boolean; schifter: boolean;
  wechsel: boolean;
}

export interface VorlagenZimmerer {
  dachstuhltyp: string;
  flags: ZimmererFlags;
  querschnittSparrenCm: [number, number];
  querschnittPfetteCm?: [number, number];
  querschnittGratsparrenCm?: [number, number];
  materialFestigkeit: string;
  holzfeuchteProzent: string;
  sparrenabstandCm: number;
  abbundhinweis: string;
  spannweiteHinweis: string;
  lastabtragsweg: string;
}

export interface VorlagenDachdecker {
  // Korrektur (deckungsneutral): KEINE feste Dacheindeckung/kein Material/kein Produkt.
  // Die Dacheindeckung wird ausschließlich über die separate Produktauswahl gewählt.
  deckungsHinweis: string;
  dachdeckungSeparatAuswaehlen: true;
  regeldachneigungAbhaengigVonMaterial: boolean; // RDN ist produkt-/materialabhängig
  lattmassAbhaengigVonProdukt: boolean; // Deckmaß/Lattung ist produktabhängig
  rdnGrad: number; // Regeldachneigung als allgemeiner RICHTWERT (produktabhängig zu prüfen)
  mindestneigungGrad: number; // RICHTWERT (produktabhängig zu prüfen)
  battenDistCm: number;
  konterlattungMm: [number, number];
  unterdeckungKlasse: string;
  firstausbildung: string;
  gratausbildung?: string;
  kehlausbildung?: string;
  ortgangausbildung: string;
  traufausbildung: string;
  entwaesserungHinweis: string;
  schneefangHinweis: string;
  lueftungHinweis: string;
}

export interface VorlagenPv {
  belegbareSeiten: string[];
  ausgeschlosseneSeiten: string[];
  sperrzoneFirstM: number;
  sperrzoneTraufeM: number;
  sperrzoneOrtgangM: number;
  sperrzoneGratKehleM?: number;
  empfohleneAusrichtung: 'portrait' | 'landscape';
  marginCm: number;
  ukTyp: string;
  flachdachReihenabstandGeplant?: boolean;
  statikPlausibilitaetHinweis: string;
}

export interface VorlagenBuildPatch {
  category: RoofCategory;
  shape: EngineRoofShape;
  length: number; // m
  width: number; // m
  height: number; // m — Traufhöhe
  pitch: number; // Grad
  overhang: number; // m
  overhangGable: number; // m
  lengthB?: number; // m — Seitenflügel-/Stiel-Tiefe (nur L/T/U-Grundriss)
  widthB?: number; // m — Quer-/Basisriegel-Tiefe (nur L/T/U-Grundriss)
  rafterSpacing: number; // cm
  rafterWidth: number; // cm
  rafterHeight: number; // cm
  battenDist: number; // cm
  attika?: number; // m, optional (Flachdach)
}

export interface VorlagenApply {
  build: VorlagenBuildPatch;
  // Korrektur: KEINE Dacheindeckung/cover mehr. Dachform-Vorlagen sind deckungsneutral;
  // das Deckmaterial wird ausschließlich über die separate Produktauswahl gewählt.
}

// Aufbau-Auto-Platzierung: Arten exakt wie ObstacleType im Planer (nur sicher unterstützte).
// A-28: hier stand dieselbe Aufzaehlung ein zweites Mal, zeichengleich mit `AufbauArt`
// (md5 der Werteliste beide 35ed563c…). Der Kommentar darueber hat es die ganze Zeit gesagt —
// „Arten exakt wie ObstacleType (nur sicher unterstuetzte)" —, nur stand daneben eine eigene
// Liste statt einer Ableitung. Jetzt ist `AufbauArt` selbst aus `ObstacleType` abgeleitet, und
// dieser Name ist nur noch ein Verweis darauf. Der NAME bleibt, weil ihn die Vorlagen benutzen.
export type VorlagenAufbauArt = AufbauArt;

/**
 * Ein beim Anwenden automatisch zu setzender Standard-Aufbau (Obstacle).
 * Maße strikt: breiteM = u (parallel Traufe), hoeheM = v (Traufe->First),
 * tiefeM = Aufbauhöhe (depth, NICHT Öffnungshöhe — Konvention aus Reparatur 9/10).
 * surfaceId = echte Dachflächen-Kennung der Engine (z. B. 'main_S', 'main').
 */
export interface VorlagenAufbau {
  art: VorlagenAufbauArt;
  surfaceId: string;
  xRel: number; // 0..1 (u)
  yRel: number; // 0..1 (v)
  breiteM: number;
  hoeheM: number;
  tiefeM: number;
  pitchGrad: number;
}

export type VorlagenWarnungCode =
  | 'WALM_INKONSISTENT' | 'NEIGUNG_UNTER_RDN' | 'NEIGUNG_UNTER_MINDEST'
  | 'EINDECKUNG_KATEGORIE' | 'PITCH_GEKLEMMT' | 'PULT_GEFAELLE';

export interface VorlagenWarnung {
  code: VorlagenWarnungCode;
  schwere: 'info' | 'warnung' | 'fehler';
  text: string;
}

export interface VorlagenValidierung {
  ok: boolean;
  warnungen: VorlagenWarnung[];
}

/**
 * Spiegelt EXAKT die Engine-BuildingParams-Felder. r.build wird unverändert an
 * setBuild übergeben — additiv aus { ...prevBuild, ...vorlage.apply.build }.
 */
export interface BuildingParamsLike {
  category: RoofCategory;
  shape: EngineRoofShape;
  length: number;
  width: number;
  height: number;
  pitch: number;
  attika: number;
  overhang: number;
  overhangGable: number;
  lengthB: number;
  widthB: number;
  layerSpread: number;
  rafterSpacing: number;
  rafterWidth: number;
  rafterHeight: number;
  battenDist: number;
}

export interface VorlagenApplyResult {
  ok: boolean;
  grund?: string;
  warnungen: VorlagenWarnung[];
  build?: BuildingParamsLike;
  // Standard-Aufbauten, die beim Anwenden zusätzlich gesetzt werden sollen (Kamin/Dachfenster/
  // Lüfter/Sat/Lichtkuppel/Gauben), bereits FLÄCHENABHÄNGIG platziert (Maße/Position an die echte
  // Zielfläche gekoppelt; pruefpflichtig-Flag bei zu kleiner/enger Fläche). KEIN cover/Material.
  aufbauten?: PlatzierterAufbau[];
  // Linienförmige Dachbauteile (Schneefang), flächenabhängig parallel zur Traufe gesetzt
  // (Länge = verfügbare Breite − Randabstände; PV-Sperrzone vorbereitet). KEIN cover/Material.
  linienBauteile?: DachLinienBauteil[];
}

// =====================================================================
// 2. MATHEMATIK / GEOMETRIE (rein, Trig in Bogenmaß)
// =====================================================================

/** endlicher Zahlenwert oder 0 — verhindert NaN/Infinity in jedem Ergebnis. */
function endlich(n: number): number {
  return Number.isFinite(n) ? n : 0;
}

/** Eigene Winkelumrechnung Grad → Bogenmaß. */
export function gradToRad(grad: number): number {
  return endlich(grad) * Math.PI / 180;
}

/** Bogenmaß → Grad. */
export function radToGrad(rad: number): number {
  return endlich(rad) * 180 / Math.PI;
}

/** tan(alpha) sicher in Bogenmaß (NaN/Infinity → 0). */
function tanGrad(grad: number): number {
  return endlich(Math.tan(gradToRad(grad)));
}

/**
 * Höhe der ÄUSSEREN Traufkante. H ist die Auflager-/Wandlinie (NICHT die äußere
 * Kante = Engine yEaveEdge). y = H_trauf − overhangHoriz·tan(alpha).
 * pitch = 0 → äußere Traufkante = Traufhöhe.
 */
export function aeussereTraufkanteHoeheM(traufhoeheAuflagerM: number, overhangHorizM: number, pitchGrad: number): number {
  return endlich(traufhoeheAuflagerM - overhangHorizM * tanGrad(pitchGrad));
}

/**
 * Waagerecht gemessene Länge → geneigte Länge: L/cos(alpha). NUR für waagerecht
 * gemessenen Trauf-Überstand. Bereits geneigt gemessen → NICHT verwenden.
 */
export function geneigteLaengeAusWaagerechtM(waagerechtM: number, pitchGrad: number): number {
  return endlich(waagerechtM / sichererCos(pitchGrad));
}

/**
 * Flächenlänge in First-/Traufrichtung inkl. Ortgang-Überstand: L + 2·overhangGable.
 * Ortgang liegt in First-/Traufrichtung → NIE /cos.
 */
export function ortgangFlaechenlaengeM(lengthM: number, overhangGableM: number): number {
  return endlich(lengthM + 2 * overhangGableM);
}

/** Satteldach-Sparrenlänge (= Engine slopeLen): (width/2 + overhang)/cos(alpha). */
export function sattelSparrenlaengeM(widthM: number, overhangTraufM: number, pitchGrad: number): number {
  return endlich((widthM / 2 + overhangTraufM) / sichererCos(pitchGrad));
}

/** Firsthöhen-Zuwachs über Traufhöhe beim Sattel: rise = (width/2)·tan(alpha). */
export function sattelFirstRiseM(widthM: number, pitchGrad: number): number {
  return endlich((widthM / 2) * tanGrad(pitchGrad));
}

/**
 * Pultdach-Sparrenlänge (= Engine vMax): (width + 2·overhang)/cos(alpha).
 * Gefälle läuft über die BREITE (Engine fix). Seitliche Überstände NICHT /cos.
 */
export function pultSparrenlaengeM(widthM: number, overhangTraufM: number, pitchGrad: number): number {
  return endlich((widthM + 2 * overhangTraufM) / sichererCos(pitchGrad));
}

/** Pultdach-Höhenzuwachs: rise = width·tan(alpha) (EIN Gefälle, volle Breite). */
export function pultRiseM(widthM: number, pitchGrad: number): number {
  return endlich(widthM * tanGrad(pitchGrad));
}

/** Walm: Schnitt-/Firsthöhe über Traufebene aus Hauptneigung: (width/2)·tan(alpha_haupt). */
export function walmFirstRiseM(widthM: number, hauptPitchGrad: number): number {
  return endlich((widthM / 2) * tanGrad(hauptPitchGrad));
}

/**
 * Walmrücksprung R = h_walm / tan(alpha_walm). Nur bei alpha_walm = alpha_haupt
 * gilt R = width/2.
 */
export function walmRuecksprungM(walmFirstRiseMVal: number, walmPitchGrad: number): number {
  const t = tanGrad(walmPitchGrad);
  if (t <= 0) return 0;
  return endlich(walmFirstRiseMVal / t);
}

/**
 * Firstlänge des Walms: L − 2·R. Rückgabe DARF <0 sein (Signal für Inkonsistenz),
 * wird NICHT still auf 0 geklemmt.
 */
export function walmFirstlaengeM(lengthM: number, ruecksprungM: number): number {
  return endlich(lengthM - 2 * ruecksprungM);
}

/**
 * Engine-Spezialfall ridgeLen = max(0, L − W) — gilt NUR bei gleicher Haupt-/
 * Walmneigung. RICHTWERT (gekennzeichnet), nicht pauschal für jedes Walmdach.
 */
export function walmFirstlaengeGleicheNeigungM(lengthM: number, widthM: number): number {
  return endlich(Math.max(0, lengthM - widthM));
}

/** Echte 3D-Diagonale (Traufeck → First-Ende), NIE waagerechte Projektion. */
export function gratsparrenLaenge3DM(dx: number, dy: number, dz: number): number {
  return endlich(Math.sqrt(dx * dx + dy * dy + dz * dz));
}

/** Geneigte Fläche aus Grundrissprojektion rechteckiger Flächen: A_grundriss/cos(alpha). */
export function geneigteFlaecheAusGrundrissM2(grundrissProjM2: number, pitchGrad: number): number {
  if (!Number.isFinite(grundrissProjM2)) return 0;
  return endlich(grundrissProjM2 / sichererCos(pitchGrad));
}

/**
 * Echte geneigte Dachfläche aus u/v-Polygon — Wiederverwendung der Shoelace-
 * Flächenformel (Reparatur 6). NIE width*height bei Trapez/Dreieck (Walm).
 */
export function dachflaecheAusPolygonM2(punkte: ReadonlyArray<Punkt2D>): number {
  return polygonFlaecheM2(punkte);
}

/**
 * Sparrenfeld-Aufteilung über die Trauflänge.
 * felder = ceil(L/e_max), sparrenAnzahl = felder+1, eff = L/felder (<= e_max).
 * Hinweis: die Engine nutzt floor → eff kann dort nominal leicht überschreiten →
 * RICHTWERT.
 */
export function sparrenfeldAufteilung(
  trauflaengeM: number,
  eMaxM: number,
): { felder: number; sparrenAnzahl: number; effektiverAbstandM: number } {
  const L = Number.isFinite(trauflaengeM) && trauflaengeM > 0 ? trauflaengeM : 0;
  const e = Number.isFinite(eMaxM) && eMaxM > 0 ? eMaxM : 0.05;
  const felder = Math.max(1, Math.ceil(L / e));
  const sparrenAnzahl = felder + 1;
  const effektiverAbstandM = endlich(L / felder);
  return { felder, sparrenAnzahl, effektiverAbstandM };
}

/**
 * Effektiver Sparrenabstand in Metern aus cm-Eingabe — geklemmt auf den
 * Engine-Floor (Reparatur 1), damit Geometrie und Stückliste denselben Wert
 * nutzen und keine Division durch ~0 entsteht.
 */
export function effektiverSparrenabstandM(rafterSpacingCm: number): number {
  return cmZuMFloor(rafterSpacingCm, DACH_FLOOR_CM.rafterSpacing / 100);
}

/**
 * Schutz gegen cos→0 / tan→∞ und NaN/Infinity. geklemmt=true löst eine sichtbare
 * PITCH_GEKLEMMT-Warnung aus (kein stilles Abschneiden). Flachdach: min=1.5, max=8.
 */
export function clampPitchGrad(
  pitchGrad: number,
  min = 1,
  max = 85,
): { wert: number; geklemmt: boolean } {
  if (!Number.isFinite(pitchGrad)) return { wert: min, geklemmt: true };
  if (pitchGrad < min) return { wert: min, geklemmt: true };
  if (pitchGrad > max) return { wert: max, geklemmt: true };
  return { wert: pitchGrad, geklemmt: false };
}

/** Walm-Konsistenz bei gleicher Neigung: L > W (sonst Firstlänge <= 0). */
export function walmIstKonsistent(lengthM: number, widthM: number): boolean {
  return Number.isFinite(lengthM) && Number.isFinite(widthM) && lengthM > widthM;
}

const PITCHED_COVER: RoofCovering[] = ['ziegel', 'schiefer', 'trapezblech'];
const FLAT_COVER: RoofCovering[] = ['bitumen', 'kunststoff', 'gruendach', 'kies'];

/** Passt die Eindeckung zur Dachkategorie (pitched vs. flat)? */
export function eindeckungPasstZuKategorie(cover: RoofCovering, category: RoofCategory): boolean {
  return category === 'flat' ? FLAT_COVER.includes(cover) : PITCHED_COVER.includes(cover);
}

/**
 * Neigungs-Prüfung gegen RICHTWERTE: pitch < rdn → NEIGUNG_UNTER_RDN
 * (Zusatzmaßnahmen-Pflicht); pitch < mindest → NEIGUNG_UNTER_MINDEST (warnt,
 * sperrt nicht).
 */
export function neigungBrauchtZusatzmassnahme(
  pitchGrad: number,
  mindestneigungGrad: number,
  rdnGrad: number,
): VorlagenWarnung[] {
  const out: VorlagenWarnung[] = [];
  if (Number.isFinite(pitchGrad) && pitchGrad < rdnGrad) {
    out.push({
      code: 'NEIGUNG_UNTER_RDN',
      schwere: 'warnung',
      text: `Dachneigung ${pitchGrad}° liegt unter der Regeldachneigung (Richtwert ${rdnGrad}°). Zusatzmaßnahme erforderlich (z. B. regensicheres Unterdach / dichtgelegte Überdeckung).`,
    });
  }
  if (Number.isFinite(pitchGrad) && pitchGrad < mindestneigungGrad) {
    out.push({
      code: 'NEIGUNG_UNTER_MINDEST',
      schwere: 'warnung',
      text: `Dachneigung ${pitchGrad}° liegt unter der Mindestneigung (Richtwert ${mindestneigungGrad}°) der gewählten Eindeckung. Eindeckung/Abdichtung prüfen.`,
    });
  }
  return out;
}

// =====================================================================
// 3. VALIDIERUNG
// =====================================================================

/**
 * Sammelt Walm-/Eindeckungs-/Neigungs-/Pult-/Pitch-Warnungen einer Vorlage.
 * Optionale eingabe übersteuert die Default-/Apply-Werte (Live-Vorschau in der UI).
 * ok = keine Warnung der Schwere 'fehler'.
 */
export function validateVorlage(v: DachformVorlage, eingabe?: Partial<VorlagenBuildPatch>): VorlagenValidierung {
  const warnungen: VorlagenWarnung[] = [];
  const g = v.geometrie;
  const cat: RoofCategory = eingabe?.category ?? g.category;
  const pitch = eingabe?.pitch ?? v.apply?.build.pitch ?? g.defaultPitch;
  const length = eingabe?.length ?? v.apply?.build.length ?? g.defaultLength;
  const width = eingabe?.width ?? v.apply?.build.width ?? g.defaultWidth;
  // Korrektur: KEINE Eindeckungs-/Material-Prüfung mehr — Dachform-Vorlagen sind
  // deckungsneutral. Die Eignung der gewählten Eindeckung wird in der Produktauswahl geprüft.

  // Neigung gegen (produktabhängige) Richtwerte
  warnungen.push(...neigungBrauchtZusatzmassnahme(pitch, v.dachdecker.mindestneigungGrad, v.dachdecker.rdnGrad));

  // Walm-Konsistenz (L > W bei gleicher Neigung)
  if ((g.engineShape === 'walm' || g.shapeKey === 'walm') && g.benoetigtLaengerGleichBreite) {
    if (!walmIstKonsistent(length, width)) {
      warnungen.push({
        code: 'WALM_INKONSISTENT',
        schwere: 'fehler',
        text: `Walmdach inkonsistent: Länge (${length} m) muss größer als Breite (${width} m) sein, sonst ergibt sich keine positive Firstlänge.`,
      });
    }
  }

  // Pult-Gefällerichtung (Engine fix 'breite')
  if (g.engineShape === 'pult' && g.gefaelleRichtung === 'laenge') {
    warnungen.push({
      code: 'PULT_GEFAELLE',
      schwere: 'warnung',
      text: 'Pult-Gefälle über die Länge wird von der Engine nicht abgebildet (nur über die Breite). Vorlage als geplant führen.',
    });
  }

  // Pitch-Klemmung
  const clamp = cat === 'flat' ? clampPitchGrad(pitch, 1.5, 8) : clampPitchGrad(pitch, 1, 85);
  if (clamp.geklemmt) {
    warnungen.push({
      code: 'PITCH_GEKLEMMT',
      schwere: 'warnung',
      text: `Dachneigung ${pitch}° wurde auf den gültigen Bereich (${clamp.wert}°) geklemmt.`,
    });
  }

  return { ok: !warnungen.some((w) => w.schwere === 'fehler'), warnungen };
}

// =====================================================================
// 4. SUCHE / FILTER / SELEKTOREN
// =====================================================================

/** Case-insensitive Treffer in name/schlagworte/shapeKey/kurzbeschreibung. */
export function sucheVorlagen(vorlagen: DachformVorlage[], query: string): DachformVorlage[] {
  const q = (query ?? '').trim().toLowerCase();
  if (!q) return vorlagen.slice();
  return vorlagen.filter((v) => {
    const haystack = [
      v.name,
      v.kurzbeschreibung,
      v.geometrie.shapeKey,
      ...v.schlagworte,
    ].join(' ').toLowerCase();
    return haystack.includes(q);
  });
}

/** Additive UND-Filter (Status, Kategorie, Form). */
export function filterVorlagen(
  vorlagen: DachformVorlage[],
  f: { status?: VorlagenStatus; category?: RoofCategory; shapeKey?: VorlagenShapeKey },
): DachformVorlage[] {
  return vorlagen.filter((v) => {
    if (f.status && v.status !== f.status) return false;
    if (f.category && v.geometrie.category !== f.category) return false;
    if (f.shapeKey && v.geometrie.shapeKey !== f.shapeKey) return false;
    return true;
  });
}

export function nurVerfuegbare(v: DachformVorlage[]): DachformVorlage[] {
  return v.filter((x) => x.status === 'verfuegbar');
}

export function nurGeplante(v: DachformVorlage[]): DachformVorlage[] {
  return v.filter((x) => x.status === 'geplant');
}

export function findeVorlage(v: DachformVorlage[], id: string): DachformVorlage | undefined {
  return v.find((x) => x.id === id);
}

/** Engine baut die Form WIRKLICH (sattel/pult/walm/rect) und apply ist gesetzt. */
export function istAnwendbar(v: DachformVorlage): boolean {
  if (v.status !== 'verfuegbar' || v.apply == null || v.geometrie.engineShape == null) return false;
  const s = v.geometrie.engineShape;
  if (s === 'sattel' || s === 'pult' || s === 'walm' || s === 'rect') return true;
  // Eingabeaufforderung 13: zusammengesetzte Grundrisse NUR als FLACHDACH anwendbar (echtes
  // Polygon in buildFlat). Geneigte L/T/U bleiben geplant (Dachverschneidungen noch nicht sicher).
  if ((s === 'l-shape' || s === 't-shape' || s === 'u-shape') && v.geometrie.category === 'flat') return true;
  return false;
}

// =====================================================================
// 4b. BILDVORSCHAU — reine, schematische Inline-SVG (KEINE externen Bilder)
// =====================================================================
// vorschauSvg(v) liefert einen vollständigen SVG-String, der je nach Form
// (shapeKey/category) + Schlagworten (gaube/kamin/dachfenster/pv/anbau …) ein
// fachlich passendes Schaubild zeichnet. Rein: keine externen Bilder, keine
// DOM-/THREE-Zugriffe. Geplante Vorlagen werden gedämpft (graue Linien)
// dargestellt. Alle Koordinaten werden über svgN() endlich und >= 0 gehalten
// (kein NaN/Infinity/negativ).

const SVG_W = 132;
const SVG_H = 96;

/** Sanitisiert eine SVG-Zahl: endlich und >= 0 (sonst 0), auf 2 Nachkommastellen gerundet. */
function svgN(x: number): number {
  if (!Number.isFinite(x) || x < 0) return 0;
  return Math.round(x * 100) / 100;
}

/** Minimal-Escaping für Text in SVG-Attributen (&,<,>,",'). */
function svgEsc(s: string): string {
  return String(s)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');
}

interface SvgPalette {
  linie: string; roofA: string; roofB: string; wand: string;
  pv: string; pvLinie: string; kamin: string; fenster: string; gaube: string; weiss: string;
}

function svgRect(x: number, y: number, w: number, h: number, fill: string, stroke: string, sw = 1, rx = 0): string {
  return `<rect x="${svgN(x)}" y="${svgN(y)}" width="${svgN(w)}" height="${svgN(h)}" rx="${svgN(rx)}" fill="${fill}" stroke="${stroke}" stroke-width="${svgN(sw)}"/>`;
}
function svgPoly(pts: ReadonlyArray<readonly [number, number]>, fill: string, stroke: string, sw = 1): string {
  const p = pts.map(([x, y]) => `${svgN(x)},${svgN(y)}`).join(' ');
  return `<polygon points="${p}" fill="${fill}" stroke="${stroke}" stroke-width="${svgN(sw)}" stroke-linejoin="round"/>`;
}
function svgLine(x1: number, y1: number, x2: number, y2: number, stroke: string, sw = 1): string {
  return `<line x1="${svgN(x1)}" y1="${svgN(y1)}" x2="${svgN(x2)}" y2="${svgN(y2)}" stroke="${stroke}" stroke-width="${svgN(sw)}" stroke-linecap="round"/>`;
}
function svgCircle(cx: number, cy: number, r: number, fill: string, stroke: string, sw = 1): string {
  return `<circle cx="${svgN(cx)}" cy="${svgN(cy)}" r="${svgN(r)}" fill="${fill}" stroke="${stroke}" stroke-width="${svgN(sw)}"/>`;
}
function svgArcPath(x1: number, y1: number, cx: number, cy: number, x2: number, y2: number, fill: string, stroke: string, sw = 1): string {
  const d = `M ${svgN(x1)} ${svgN(y1)} Q ${svgN(cx)} ${svgN(cy)} ${svgN(x2)} ${svgN(y2)}`;
  return `<path d="${d}" fill="${fill}" stroke="${stroke}" stroke-width="${svgN(sw)}" stroke-linejoin="round"/>`;
}

// --- Basis-Dachkörper (schematische Frontansichten bzw. Grundrisse) ---
function svgSattel(p: SvgPalette): string {
  return [
    svgRect(28, 54, 76, 30, p.wand, p.linie, 1.2),
    svgPoly([[18, 55], [66, 24], [66, 55]], p.roofA, p.linie, 1.4),
    svgPoly([[66, 24], [114, 55], [66, 55]], p.roofB, p.linie, 1.4),
    svgLine(66, 24, 66, 55, p.linie, 1.2),
    svgLine(16, 55, 116, 55, p.linie, 1.2),
  ].join('');
}
function svgPult(p: SvgPalette): string {
  return [
    svgRect(30, 54, 72, 30, p.wand, p.linie, 1.2),
    svgPoly([[24, 38], [108, 52], [108, 55], [24, 55]], p.roofA, p.linie, 1.4),
    svgLine(24, 38, 108, 52, p.linie, 1.2),
    svgLine(22, 55, 110, 55, p.linie, 1),
  ].join('');
}
function svgWalm(p: SvgPalette): string {
  return [
    svgRect(28, 54, 76, 30, p.wand, p.linie, 1.2),
    svgPoly([[30, 55], [50, 30], [82, 30], [102, 55]], p.roofA, p.linie, 1.4),
    svgPoly([[18, 55], [30, 55], [50, 30]], p.roofB, p.linie, 1.4),
    svgPoly([[102, 55], [114, 55], [82, 30]], p.roofB, p.linie, 1.4),
    svgLine(50, 30, 82, 30, p.linie, 1.2),
    svgLine(16, 55, 116, 55, p.linie, 1),
  ].join('');
}
function svgFlach(p: SvgPalette, attika: boolean): string {
  return [
    svgRect(28, 48, 76, 36, p.wand, p.linie, 1.2),
    svgRect(24, 42, 84, 8, p.roofA, p.linie, 1.4, 1),
    attika ? svgRect(24, 39, 84, 4, p.roofB, p.linie, 1, 1) : '',
    svgLine(24, 42, 108, 42, p.linie, 1.2),
  ].join('');
}
function svgPyramide(p: SvgPalette): string {
  return [
    svgRect(34, 54, 64, 30, p.wand, p.linie, 1.2),
    svgPoly([[28, 55], [66, 22], [66, 55]], p.roofA, p.linie, 1.4),
    svgPoly([[66, 22], [104, 55], [66, 55]], p.roofB, p.linie, 1.4),
    svgLine(28, 55, 66, 22, p.linie, 1.2),
    svgLine(104, 55, 66, 22, p.linie, 1.2),
    svgLine(16, 55, 116, 55, p.linie, 1),
  ].join('');
}
function svgMansard(p: SvgPalette): string {
  return [
    svgRect(30, 58, 72, 26, p.wand, p.linie, 1.2),
    svgPoly([[22, 58], [34, 40], [98, 40], [110, 58]], p.roofA, p.linie, 1.4),
    svgPoly([[34, 40], [66, 28], [98, 40]], p.roofB, p.linie, 1.4),
    svgLine(34, 40, 98, 40, p.linie, 1),
    svgLine(20, 58, 112, 58, p.linie, 1),
  ].join('');
}
function svgShed(p: SvgPalette): string {
  const teeth: string[] = [];
  for (let i = 0; i < 4; i++) {
    const x = 24 + i * 22;
    teeth.push(svgPoly([[x, 56], [x, 40], [x + 18, 56]], i % 2 === 0 ? p.roofA : p.roofB, p.linie, 1.2));
    teeth.push(svgLine(x, 40, x, 56, p.linie, 1));
  }
  return [svgRect(24, 56, 84, 28, p.wand, p.linie, 1.2), ...teeth, svgLine(22, 56, 110, 56, p.linie, 1)].join('');
}
function svgTonneBogen(p: SvgPalette): string {
  return [
    svgRect(28, 56, 76, 28, p.wand, p.linie, 1.2),
    svgArcPath(22, 56, 66, 18, 110, 56, p.roofA, p.linie, 1.6),
    svgLine(20, 56, 112, 56, p.linie, 1),
  ].join('');
}
function svgSchmetterling(p: SvgPalette): string {
  return [
    svgRect(28, 52, 76, 32, p.wand, p.linie, 1.2),
    svgPoly([[22, 38], [66, 52], [66, 56], [22, 42]], p.roofA, p.linie, 1.4),
    svgPoly([[110, 38], [66, 52], [66, 56], [110, 42]], p.roofB, p.linie, 1.4),
    svgLine(66, 52, 66, 56, p.linie, 1),
  ].join('');
}
function svgGraben(p: SvgPalette): string {
  const teile: string[] = [];
  // Aneinandergereihte, gegenläufige Satteldächer; die Kehlen dazwischen (Tiefpunkte) sind die „Gräben".
  for (let i = 0; i < 3; i++) {
    const x = 22 + i * 30;
    teile.push(svgPoly([[x, 54], [x + 15, 39], [x + 30, 54]], i % 2 === 0 ? p.roofA : p.roofB, p.linie, 1.2));
  }
  // Innenliegende Tiefpunkte/Kehlrinnen markieren (das namensgebende Merkmal des Grabendachs)
  teile.push(svgCircle(52, 53, 1.7, p.fenster, p.linie, 0.6), svgCircle(82, 53, 1.7, p.fenster, p.linie, 0.6));
  return [svgRect(20, 54, 92, 30, p.wand, p.linie, 1.2), ...teile, svgLine(18, 54, 114, 54, p.linie, 1)].join('');
}
function svgHalle(p: SvgPalette): string {
  return [
    svgRect(16, 52, 100, 32, p.wand, p.linie, 1.2),
    svgPoly([[12, 53], [66, 40], [120, 53]], p.roofA, p.linie, 1.4),
    svgLine(12, 53, 120, 53, p.linie, 1),
  ].join('');
}
function svgGrundriss(kind: 'l' | 't' | 'u', p: SvgPalette): string {
  let pts: ReadonlyArray<readonly [number, number]>;
  if (kind === 'l') pts = [[26, 24], [74, 24], [74, 52], [110, 52], [110, 84], [26, 84]];
  else if (kind === 't') pts = [[24, 24], [110, 24], [110, 48], [82, 48], [82, 84], [52, 84], [52, 48], [24, 48]];
  else pts = [[24, 24], [48, 24], [48, 60], [86, 60], [86, 24], [110, 24], [110, 84], [24, 84]];
  return [
    svgPoly(pts, p.wand, p.linie, 1.6),
    svgRect(34, 34, 14, 14, p.roofA, p.linie, 0.8, 1),
    svgRect(60, 60, 14, 14, p.roofB, p.linie, 0.8, 1),
  ].join('');
}
function svgMehrfluegel(p: SvgPalette): string {
  const pts: ReadonlyArray<readonly [number, number]> = [
    [54, 22], [80, 22], [80, 46], [110, 46], [110, 72], [80, 72], [80, 86], [54, 86], [54, 72], [24, 72], [24, 46], [54, 46],
  ];
  return svgPoly(pts, p.wand, p.linie, 1.6);
}
function svgMehrkoerper(p: SvgPalette): string {
  return [
    svgRect(18, 50, 46, 34, p.wand, p.linie, 1.2),
    svgPoly([[14, 51], [41, 32], [68, 51]], p.roofA, p.linie, 1.3),
    svgLine(41, 32, 41, 51, p.linie, 1),
    svgRect(72, 58, 42, 26, p.wand, p.linie, 1.2),
    svgPoly([[68, 59], [93, 42], [118, 59]], p.roofB, p.linie, 1.3),
    svgLine(93, 42, 93, 59, p.linie, 1),
    svgLine(14, 84, 118, 84, p.linie, 1),
  ].join('');
}

// --- Sonderformen mit EIGENER Silhouette (nicht als Walm/Pult/Mansard-Fallback) ---
function svgKrueppelwalm(p: SvgPalette): string {
  // Schopf-/Krüppelwalm: Haupttrapez + kleiner abgewalmter Schopf oben unter dem First,
  // unten senkrechte Giebeldreiecke (der „Krüppel"). Klar verschieden vom Vollwalm.
  return [
    svgRect(28, 54, 76, 30, p.wand, p.linie, 1.2),
    svgPoly([[30, 55], [48, 33], [84, 33], [102, 55]], p.roofA, p.linie, 1.4),
    svgPoly([[48, 33], [58, 27], [74, 27], [84, 33]], p.roofB, p.linie, 1.2),
    svgPoly([[30, 55], [48, 33], [48, 55]], p.roofB, p.linie, 1.0),
    svgPoly([[84, 55], [84, 33], [102, 55]], p.roofB, p.linie, 1.0),
    svgLine(58, 27, 74, 27, p.linie, 1.2),
    svgLine(48, 33, 84, 33, p.linie, 0.8),
    svgLine(16, 55, 116, 55, p.linie, 1),
  ].join('');
}
function svgMansardwalm(p: SvgPalette): string {
  // Mansard-Doppelneigung + abgewalmte Stirnseiten (verschieden vom reinen Mansarddach).
  return [
    svgRect(30, 58, 72, 26, p.wand, p.linie, 1.2),
    svgPoly([[34, 58], [44, 40], [88, 40], [98, 58]], p.roofA, p.linie, 1.4),
    svgPoly([[44, 40], [66, 30], [88, 40]], p.roofB, p.linie, 1.4),
    svgPoly([[22, 58], [34, 58], [44, 40]], p.roofB, p.linie, 1.2),
    svgPoly([[98, 58], [110, 58], [88, 40]], p.roofB, p.linie, 1.2),
    svgLine(44, 40, 88, 40, p.linie, 0.8),
    svgLine(20, 58, 112, 58, p.linie, 1),
  ].join('');
}
function svgVersetztesPult(p: SvgPalette): string {
  // Zwei höhenversetzte Pultflächen + vertikales Fenster-/Lichtband am Versatz.
  return [
    svgRect(24, 54, 84, 30, p.wand, p.linie, 1.2),
    svgPoly([[24, 34], [62, 42], [62, 55], [24, 55]], p.roofB, p.linie, 1.3),
    svgPoly([[60, 44], [104, 52], [104, 55], [60, 55]], p.roofA, p.linie, 1.3),
    svgRect(60, 33, 4, 12, p.fenster, p.linie, 0.8),
    svgLine(22, 55, 106, 55, p.linie, 1),
  ].join('');
}
function svgSchleppdach(p: SvgPalette): string {
  // Hauptdach (steiler) + angeschleppte, flachere Fläche (Schleppe) — kein reines Pult.
  return [
    svgRect(28, 54, 64, 30, p.wand, p.linie, 1.2),
    svgPoly([[24, 40], [66, 24], [66, 55], [24, 55]], p.roofA, p.linie, 1.3),
    svgPoly([[66, 34], [104, 50], [104, 55], [66, 55]], p.roofB, p.linie, 1.3),
    svgLine(66, 24, 66, 55, p.linie, 1),
    svgLine(22, 55, 106, 55, p.linie, 1),
  ].join('');
}

// --- Aufbauten / PV als Overlays (schematisch) ---
function svgPvFeld(x0: number, y0: number, cols: number, rows: number, p: SvgPalette): string {
  const out: string[] = [];
  const cell = 8;
  const gap = 1.5;
  for (let r = 0; r < rows; r++) {
    for (let c = 0; c < cols; c++) {
      const x = x0 + c * (cell + gap);
      const y = y0 + r * (cell + gap);
      out.push(svgRect(x, y, cell, cell * 0.7, p.pv, p.pvLinie, 0.6));
      out.push(svgLine(x + cell / 2, y, x + cell / 2, y + cell * 0.7, p.pvLinie, 0.4));
    }
  }
  return out.join('');
}
function svgKamin(x: number, baseY: number, p: SvgPalette): string {
  const h = 18;
  return svgRect(x, baseY - h, 7, h, p.kamin, p.linie, 1) + svgRect(x - 2, baseY - h - 2, 11, 4, p.kamin, p.linie, 1);
}
function svgDachfenster(x: number, y: number, p: SvgPalette): string {
  return svgRect(x, y, 9, 7, p.fenster, p.linie, 0.8, 1);
}
function svgLuefter(x: number, y: number, p: SvgPalette): string {
  return svgCircle(x, y, 3, p.roofB, p.linie, 0.8) + svgRect(x - 1.5, y - 7, 3, 7, p.roofB, p.linie, 0.6);
}
function svgSat(x: number, y: number, p: SvgPalette): string {
  return svgCircle(x, y, 5, p.weiss, p.linie, 1) + svgLine(x, y, x + 5, y - 4, p.linie, 1);
}
function svgSchneefang(p: SvgPalette): string {
  return svgLine(24, 50, 108, 50, p.kamin, 1.8);
}
function svgLichtkuppel(x: number, y: number, p: SvgPalette): string {
  return svgArcPath(x, y, x + 6, y - 8, x + 12, y, p.weiss, p.linie, 1);
}
type GaubeSvgTyp = 'flach' | 'schlepp' | 'walm' | 'giebel' | 'spitz' | 'tonne' | 'fledermaus' | 'zwerch' | 'rund';
function svgGaube(x: number, y: number, w: number, typ: GaubeSvgTyp, p: SvgPalette): string {
  let dach: string;
  if (typ === 'flach') dach = svgRect(x - 1, y - 3, w + 2, 4, p.roofB, p.linie, 0.8);
  else if (typ === 'schlepp') dach = svgPoly([[x - 1, y], [x + w + 1, y - 5], [x + w + 1, y]], p.roofB, p.linie, 0.8);
  else if (typ === 'walm') dach = svgPoly([[x - 1, y], [x + w * 0.25, y - 6], [x + w * 0.75, y - 6], [x + w + 1, y]], p.roofB, p.linie, 0.8);
  else if (typ === 'spitz') dach = svgPoly([[x - 1, y], [x + w / 2, y - 12], [x + w + 1, y]], p.roofB, p.linie, 0.8); // hohe, spitze Dreiecksgaube
  else if (typ === 'tonne') dach = svgArcPath(x - 1, y, x + w / 2, y - 9, x + w + 1, y, p.roofB, p.linie, 0.9); // gerundete Tonnen-/Segmentbogengaube
  else if (typ === 'rund') dach = svgArcPath(x + 1, y, x + w / 2, y - (w / 2 + 1), x + w - 1, y, p.roofB, p.linie, 0.9); // halbrunde Rundgaube
  else if (typ === 'fledermaus') {
    // Fledermausgaube: weich geschwungene, flache Aufwölbung (zwei flache Bögen, kein First).
    dach = svgArcPath(x - 2, y + 1, x + w / 2, y - 4, x + w + 2, y + 1, p.roofB, p.linie, 0.9)
      + svgArcPath(x - 2, y + 1, x + w / 2, y - 1, x + w + 2, y + 1, p.gaube, p.linie, 0.5);
  } else if (typ === 'zwerch') {
    // Zwerchgaube: wandbündiger, hoher Aufbau mit eigenem Giebeldach + kleinem Überstand.
    const dachG = svgPoly([[x - 2, y], [x + w / 2, y - 9], [x + w + 2, y]], p.roofB, p.linie, 0.9);
    const koerper = svgRect(x, y, w, 16, p.gaube, p.linie, 1); // höher = bis zur Traufe (Zwerch)
    const fensterZ = svgRect(x + 2, y + 3, w - 4, 9, p.fenster, p.linie, 0.6, 0.5);
    return dachG + koerper + fensterZ;
  } else dach = svgPoly([[x - 1, y], [x + w / 2, y - 7], [x + w + 1, y]], p.roofB, p.linie, 0.8); // giebel/satteldach
  const body = svgRect(x, y, w, 12, p.gaube, p.linie, 1);
  const fenster = svgRect(x + 2, y + 3, w - 4, 6, p.fenster, p.linie, 0.6, 0.5);
  return dach + body + fenster;
}
/** Garagentor (Sektionaltor) an der Stirnwand — macht eine Garagen-Vorlage als solche erkennbar. */
function svgGarageTor(cx: number, baseY: number, p: SvgPalette): string {
  const w = 20; const h = 16; const x = cx - w / 2; const y = baseY - h;
  return [
    svgRect(x, y, w, h, p.gaube, p.linie, 1, 0.5),
    svgLine(x, y + 5.3, x + w, y + 5.3, p.linie, 0.5),
    svgLine(x, y + 10.6, x + w, y + 10.6, p.linie, 0.5),
  ].join('');
}
/** Carport = offener Nebenkörper: Stützen + Querriegel statt geschlossener Wand. */
function svgCarportOffen(cx: number, baseY: number, p: SvgPalette): string {
  const w = 26; const x = cx - w / 2; const top = baseY - 22;
  return [
    svgRect(x - 2, top - 1, w + 4, 2.5, p.roofB, p.linie, 0.6),
    svgLine(x, top + 1.5, x, baseY, p.linie, 1.4),
    svgLine(x + w, top + 1.5, x + w, baseY, p.linie, 1.4),
  ].join('');
}

/** Dachflächen-Umriss je Form zum Clippen der PV-Felder (sonst „schweben" Module über der Schräge). */
function roofClipPts(k: VorlagenShapeKey): ReadonlyArray<readonly [number, number]> | null {
  switch (k) {
    case 'sattel': return [[16, 55], [66, 24], [116, 55]];
    case 'pult': return [[24, 38], [108, 52], [108, 55], [24, 55]];
    case 'walm': return [[16, 55], [50, 30], [82, 30], [116, 55]];
    case 'rect': return [[24, 42], [108, 42], [108, 50], [24, 50]];
    default: return null;
  }
}

// =====================================================================
// Ehrlichkeit der Vorschau: was zeigt das Bild ZUSÄTZLICH zum Basisdach,
// das „Anwenden" NICHT setzt? (Aufbauten/PV werden separat ergänzt.)
// =====================================================================
const VORSCHAU_AUFBAU_TOKENS = [
  'gaube', 'dachfenster', 'kamin', 'schornstein', 'lüfter', 'luefter', 'entlüftung',
  'rauchabzug', 'rwa', 'sat', 'satellit', 'schneefang', 'lichtkuppel', 'oberlicht', 'sperrfläche',
];

/** true, wenn die Vorschau einen physischen Aufbau (Gaube/Kamin/Dachfenster/…) zeigt. */
export function vorschauZeigtAufbau(v: DachformVorlage): boolean {
  const s = new Set(v.schlagworte.map((x) => x.toLowerCase()));
  return VORSCHAU_AUFBAU_TOKENS.some((t) => s.has(t));
}

/** true, wenn die Vorschau eine PV-Belegung andeutet (echtes PV-Token, kein Grundriss). */
export function vorschauZeigtPv(v: DachformVorlage): boolean {
  const s = new Set(v.schlagworte.map((x) => x.toLowerCase()));
  const k = v.geometrie.shapeKey;
  const isGrund = k === 'l-shape' || k === 't-shape' || k === 'u-grundriss' || k === 'mehrfluegel';
  return (s.has('pv') || s.has('photovoltaik')) && !isGrund;
}

// =====================================================================
// Aufbau-Auto-Platzierung: aus Form + Schlagworten die SICHER setzbaren
// Standard-Aufbauten (Obstacles) ableiten — gleiche Token-Logik wie die SVG-Overlays.
// Nur Arten, die der Planer als Obstacle wirklich unterstützt. Lichtkuppel/Oberlicht
// und Schneefang haben KEINEN Obstacle-Typ und bleiben Vorschau (nicht gesetzt).
// =====================================================================

// Default-Maße je Art — exakt wie im Planer (addObs). breite=u, hoehe=v, tiefe=Aufbauhöhe.
const AUFBAU_DIM: Record<VorlagenAufbauArt, { b: number; h: number; t: number; p: number }> = {
  chimney: { b: 0.6, h: 0.6, t: 0.6, p: 0 },
  window: { b: 0.78, h: 1.18, t: 0.1, p: 0 },
  vent: { b: 0.2, h: 0.2, t: 0.2, p: 0 },
  sat: { b: 0.8, h: 0.8, t: 0.8, p: 0 },
  lichtkuppel: { b: 1.0, h: 1.0, t: 0.3, p: 0 }, // Öffnungsmaß in Flächenebene; Tiefe = Aufbauhöhe
  schleppgaube: { b: 2.5, h: 1.5, t: 2.5, p: 15 },
  trapezgaube: { b: 3.0, h: 1.5, t: 2.5, p: 15 },
  flachgaube: { b: 2.5, h: 1.5, t: 2.5, p: 3 },
  giebelgaube: { b: 2.5, h: 1.5, t: 2.5, p: 35 },
};

// Single Source of Truth: shapeKey -> ECHTE Engine-Surface-IDs (wie buildSattel/Pult/Walm/Flat sie
// registrieren). Sattel: main_S/main_N; Pult/Flach: main; Walm: south/north/west/east (NICHT main_*!).
export const ENGINE_FLAECHEN: Record<string, readonly string[]> = {
  sattel: ['main_S', 'main_N'],
  pult: ['main'],
  rect: ['main'],
  walm: ['south', 'north', 'west', 'east'],
};

/** Echte Engine-Surface-ID der Hauptfläche je Form (sichere Flächenzuordnung). null = unsicher. */
function hauptflaecheId(v: DachformVorlage): string | null {
  // Zusammengesetzte Grundrisse (L/T/U/Mehrflügel/Mehrkörper) haben KEINE eindeutige rechteckige
  // Hauptfläche -> Aufbauten/Schneefang würden in der Bounding-Box ggf. in den Innenwinkel/Innenhof
  // fallen. Daher hier nicht auto-setzen (auch beim Flachdach-L/T/U). Geometrie bleibt korrekt.
  const k = v.geometrie.shapeKey;
  if (k === 'l-shape' || k === 't-shape' || k === 'u-grundriss' || k === 'mehrfluegel' || k === 'mehrkoerper') return null;
  if (v.geometrie.category === 'flat') return 'main';
  switch (k) {
    case 'pult': return 'main';
    case 'sattel': return 'main_S';
    case 'walm': return 'south'; // große Süd-Trapezfläche des Walms (Pendant zu Sattel main_S)
    default: return null; // Sonderformen: keine eindeutige Hauptfläche -> nicht auto-setzen
  }
}

/**
 * Leitet die beim Anwenden automatisch zu setzenden Standard-Aufbauten ab (rein, keine Engine).
 * Nur verfügbare Vorlagen mit eindeutiger Hauptfläche. Lichtkuppel/Oberlicht werden als eigene
 * Aufbauart gesetzt; NUR Schneefang (linienförmig, kein Punkt-Obstacle) wird NICHT gesetzt.
 * Liefert die SOLL-Wünsche (relative Position + Soll-Maße); die flächenabhängige Endplatzierung
 * macht applyVorlage über platziereAufbauten(). Positionen meiden First/Traufe-Randzonen.
 */
export function standardAufbauten(v: DachformVorlage): VorlagenAufbau[] {
  if (v.status !== 'verfuegbar') return [];
  const surfaceId = hauptflaecheId(v);
  if (!surfaceId) return [];
  const s = new Set(v.schlagworte.map((x) => x.toLowerCase()));
  const out: VorlagenAufbau[] = [];
  const add = (art: VorlagenAufbauArt, xRel: number, yRel: number, breiteM?: number) => {
    const d = AUFBAU_DIM[art];
    out.push({ art, surfaceId, xRel, yRel, breiteM: breiteM ?? d.b, hoeheM: d.h, tiefeM: d.t, pitchGrad: d.p });
  };

  // Kamin firstnah (aber außerhalb der First-Randzone von Reparatur 9).
  if (s.has('kamin') || s.has('schornstein')) add('chimney', 0.5, 0.72);

  if (s.has('dachfenster')) {
    const gaubePresent = s.has('gaube'); // Dachfenster NEBEN die Gaube legen (keine Überlappung)
    if (s.has('fenster4')) { add('window', 0.24, 0.46); add('window', 0.42, 0.46); add('window', 0.6, 0.46); add('window', 0.78, 0.46); }
    else if (gaubePresent) { add('window', 0.2, 0.5); add('window', 0.8, 0.5); }
    else { add('window', 0.36, 0.46); add('window', 0.64, 0.46); }
  }

  // Lüfter/Entlüfter/Rauchabzug firstnäher (Dunstrohre sitzen i. d. R. oben).
  if (s.has('lüfter') || s.has('luefter') || s.has('entlüftung') || s.has('rauchabzug') || s.has('rwa')) {
    add('vent', 0.42, 0.64);
    if (s.has('lüfter') || s.has('luefter') || s.has('entlüftung')) add('vent', 0.6, 0.64);
  }

  if (s.has('sat') || s.has('satellit')) add('sat', 0.74, 0.4);

  // Lichtkuppel/Oberlicht: eigene Aufbauart, auf dem Flachdach mittig (kein punktförmiger Fehl-Aufbau).
  if (s.has('lichtkuppel') || s.has('oberlicht')) add('lichtkuppel', 0.5, 0.5);

  // Nur GEOMETRISCH UNTERSTÜTZTE Gaubenarten werden als (schematisches) Obstacle gesetzt:
  // Schlepp/Satteldach(Giebel)/Walm(Trapez)/Flach. Spitz/Tonne/Fledermaus/Rund/Zwerch/Dreieck/
  // Segmentbogen sind NICHT als Obstacle abbildbar -> diese Vorlagen sind 'geplant' (Vorschau).
  const UNGESTUETZTE_GAUBE = ['spitzgaube', 'dreiecksgaube', 'tonnengaube', 'segmentbogengaube', 'bogengaube', 'fledermausgaube', 'rundgaube', 'zwerchgaube'];
  if (s.has('gaube') && !UNGESTUETZTE_GAUBE.some((t) => s.has(t))) {
    const art: VorlagenAufbauArt =
      s.has('flachgaube') || s.has('flachdachgaube') ? 'flachgaube'
        : (s.has('schleppgaube') || s.has('schlepp')) ? 'schleppgaube'
          : (s.has('walmgaube') || s.has('walmdachgaube')) ? 'trapezgaube'
            : 'giebelgaube';
    if (s.has('gaubenband') || s.has('band')) { add(art, 0.2, 0.42); add(art, 0.4, 0.42); add(art, 0.6, 0.42); add(art, 0.8, 0.42); }
    else if (s.has('drei')) { add(art, 0.25, 0.42); add(art, 0.5, 0.42); add(art, 0.75, 0.42); }
    else if (s.has('mehrere') || s.has('kleine') || s.has('zwei')) { add(art, 0.32, 0.42); add(art, 0.68, 0.42); }
    else if (s.has('breit') || s.has('mittelgaube')) { add(art, 0.5, 0.42, 4.0); }
    else { add(art, 0.5, 0.42); }
  }

  return out;
}

/** Gaubenart-Tokens, die der Planer NICHT als Obstacle setzen kann (nur Bildvorschau -> 'geplant'). */
export const UNGESTUETZTE_GAUBE_TOKENS = ['spitzgaube', 'dreiecksgaube', 'tonnengaube', 'segmentbogengaube', 'bogengaube', 'fledermausgaube', 'rundgaube', 'zwerchgaube'];

/** true, wenn die Vorlage eine (geometrisch unterstützte) Gaube als schematisches Obstacle setzt. */
export function gaubeSchematischGesetzt(v: DachformVorlage): boolean {
  if (v.status !== 'verfuegbar') return false;
  const s = new Set(v.schlagworte.map((x) => x.toLowerCase()));
  return s.has('gaube') && !UNGESTUETZTE_GAUBE_TOKENS.some((t) => s.has(t)) && standardAufbauten(v).some((a) => a.art.endsWith('gaube'));
}

/** true, wenn beim Anwenden mindestens ein Standard-Aufbau gesetzt wird. */
export function aufbautenWerdenGesetzt(v: DachformVorlage): boolean {
  return standardAufbauten(v).length > 0;
}

/**
 * true, wenn die Vorlage einen Schneefang zeigt UND er als linienförmiges Bauteil sicher auf die
 * Hauptfläche gesetzt werden kann (flächenabhängig, kein Phantom). Für Status/Hinweis.
 */
export function schneefangWirdGesetzt(v: DachformVorlage): boolean {
  if (v.status !== 'verfuegbar' || !v.apply) return false;
  const s = new Set(v.schlagworte.map((x) => x.toLowerCase()));
  if (!s.has('schneefang')) return false;
  const surfaceId = hauptflaecheId(v);
  if (!surfaceId) return false;
  return platziereSchneefang(surfaceId, hauptflaecheInfo(v.apply.build as unknown as BuildingParamsLike, surfaceId)).bauteil != null;
}

/**
 * Aufbau-Merkmale, die das Bild zeigt, aber NICHT gesetzt werden (-> 'teilweise'). Schneefang wird
 * jetzt als Linienbauteil gesetzt (sofern die Fläche es erlaubt); nur wenn das NICHT gelingt
 * (zu schmal/keine eindeutige Fläche), bleibt er offen. Lichtkuppel ist eigene Aufbauart (gesetzt).
 */
export function aufbautenNichtGesetzt(v: DachformVorlage): string[] {
  if (v.status !== 'verfuegbar') return [];
  const s = new Set(v.schlagworte.map((x) => x.toLowerCase()));
  const offen: string[] = [];
  if (s.has('schneefang') && !schneefangWirdGesetzt(v)) offen.push('Schneefang');
  if (vorschauZeigtAufbau(v) && !hauptflaecheId(v)) offen.push('Aufbau');
  return offen;
}

/**
 * true, wenn das Bild etwas zeigt, das „Anwenden" NICHT erzeugt: einen nicht setzbaren Aufbau
 * (Lichtkuppel/Schneefang) ODER die PV-Belegung (separater Schritt). Für Hinweis/Badge.
 */
export function istVorschauVersprechen(v: DachformVorlage): boolean {
  return v.status === 'verfuegbar' && (aufbautenNichtGesetzt(v).length > 0 || vorschauZeigtPv(v));
}

export type VorlagenAnzeigeStatus = 'verfuegbar' | 'teilweise' | 'geplant';

/**
 * Ehrlicher ANZEIGE-Status (Daten-Status v.status bleibt unverändert):
 * - 'geplant'    -> nicht anwendbar.
 * - 'teilweise'  -> Basisdach + setzbare Aufbauten werden gesetzt, ABER (a) ein gezeigtes Merkmal
 *                   (Schneefang) wird nicht gesetzt, ODER (b) eine GAUBE wird nur schematisch als
 *                   Aufbau gesetzt (keine echte Gaubendach-Geometrie mit Kehlen/Wangen).
 * - 'verfuegbar' -> „Anwenden" erzeugt Form + alle gezeigten (setzbaren) Aufbauten vollständig.
 *                   (PV-Belegung ist ein separater Planungsschritt und ändert den Status nicht.)
 */
export function anzeigeStatus(v: DachformVorlage): VorlagenAnzeigeStatus {
  if (v.status === 'geplant') return 'geplant';
  // Schematische Gaube (Obstacle ohne echte Gaubendach-Geometrie) -> ehrlich 'teilweise'.
  if (gaubeSchematischGesetzt(v)) return 'teilweise';
  return aufbautenNichtGesetzt(v).length > 0 ? 'teilweise' : 'verfuegbar';
}

/** Hinweis: schematische Gaube als Aufbau, KEINE echte Gaubendach-Geometrie (Kehlen/Wangen). */
export const GAUBE_SCHEMATISCH_HINWEIS =
  'Diese Vorlage setzt die Gaube lotrecht mit Gaubenkörper, Front, Wangen, Gaubendach und einem ' +
  'sauberen Anschluss an das Hauptdach (Rück-/Seiten-/Fußlinie auf der Dachfläche, keine Rückwand ' +
  'über dem First). Auf einer sicheren rechteckigen Sattel-/Pultfläche wird unter dem Gaubenfußabdruck ' +
  'ein ECHTES Hauptdach-Loch erzeugt (Nettoflächen-Abzug); auf Walm/Trapez/L-T-U bleibt es Prüffeld. ' +
  'Giebel-/Satteldachgauben auf sicheren Rechteckflächen erhalten ZWEI ebene Dachflächen und ECHTE ' +
  'Kehl-Schnittlinien (berechnete Dach∩Hauptdach-Verschneidung); Pultgauben und unsichere Fälle nutzen ' +
  'schematische Anschlusslinien. Maßhaltige Dachdecker-Kehlausbildung/Verwahrung und statische Auslegung ' +
  'fachlich prüfen. Auswechslung/Sparrentrennung greifen.';

/** Hinweis: Gaubenart ist nur als Bildvorschau vorbereitet, wird (noch) NICHT gesetzt. */
export const GAUBE_VORSCHAU_HINWEIS =
  'Diese Gaubenart ist als Bildvorschau vorbereitet, wird aber noch nicht automatisch gesetzt ' +
  '(keine passende Aufbau-Geometrie). Sichtbar als geplante Vorlage.';

/** Hinweis: setzbare Aufbauten werden automatisch + flächenabhängig angelegt (bestehende bleiben). */
export const AUFBAU_AUTO_HINWEIS =
  'Aufbauten (z. B. Kamin, Dachfenster, Lüfter, Sat, Lichtkuppel, Gaube) werden beim Anwenden ' +
  'automatisch und flächenabhängig platziert (Größe/Position an die echte Dachfläche angepasst). ' +
  'Bestehende Aufbauten bleiben erhalten; Gauben werden schematisch gesetzt (Gaubendach-Geometrie folgt später).';

/** Hinweis: Schneefang ist (noch) ein linienförmiges Bauteil ohne Punkt-Obstacle -> Vorschau. */
export const VORSCHAU_AUFBAU_HINWEIS =
  'Schneefang ist als Vorschau vorbereitet und wird noch nicht als linienförmiges Dachbauteil ' +
  'gesetzt — bitte im Reiter „Aufbauten" bzw. später als Traufdetail ergänzen.';

/** Ehrlichkeitshinweis für reine PV-Orientierungs-Vorlagen. */
export const VORSCHAU_PV_HINWEIS =
  'Die dargestellte PV-Belegung ist eine Planungsvorschau. „Anwenden" setzt nur die Dachform — ' +
  'die PV-Module werden separat in der Belegung platziert.';

/**
 * Schematische Bildvorschau einer Dachform-Vorlage als reiner SVG-String.
 * Form aus geometrie.shapeKey/category; Aufbauten/PV aus schlagworte. Geplante
 * Vorlagen werden gedämpft dargestellt. KEINE externen Bilder/Abhängigkeiten.
 */
export function vorschauSvg(v: DachformVorlage): string {
  const g = v.geometrie;
  const k = g.shapeKey;
  // Korrektur: WORTGENAUES Matching (Token-Mitgliedschaft) statt Teilstring.
  // Verhindert Fehl-Overlays wie Sat-Antenne auf jedem „sattel" oder PV durch „pv-tauglich".
  const tagSet = new Set(v.schlagworte.map((s) => s.toLowerCase()));
  const hasTok = (t: string) => tagSet.has(t);
  const geplant = v.status === 'geplant';
  const isFlat = g.category === 'flat';
  const isGrund = k === 'l-shape' || k === 't-shape' || k === 'u-grundriss' || k === 'mehrfluegel';

  const pal: SvgPalette = geplant
    ? { linie: '#94a3b8', roofA: '#e2e8f0', roofB: '#cbd5e1', wand: '#f1f5f9', pv: '#bfdbfe', pvLinie: '#93c5fd', kamin: '#cbd5e1', fenster: '#e2e8f0', gaube: '#eef2f7', weiss: '#ffffff' }
    : { linie: '#475569', roofA: '#94a3b8', roofB: '#64748b', wand: '#e2e8f0', pv: '#1d4ed8', pvLinie: '#0f2a6b', kamin: '#b45309', fenster: '#bae6fd', gaube: '#cbd5e1', weiss: '#ffffff' };

  let base: string;
  switch (k) {
    case 'sattel': base = svgSattel(pal); break;
    case 'pult': base = svgPult(pal); break;
    case 'schleppdach': base = svgSchleppdach(pal); break;
    case 'versetztes-pult': base = svgVersetztesPult(pal); break;
    case 'walm': base = svgWalm(pal); break;
    case 'krueppelwalm': base = svgKrueppelwalm(pal); break;
    case 'rect': base = svgFlach(pal, hasTok('attika')); break;
    case 'zeltdach': case 'pyramidendach': base = svgPyramide(pal); break;
    case 'mansard': base = svgMansard(pal); break;
    case 'mansardwalm': base = svgMansardwalm(pal); break;
    case 'sheddach': base = svgShed(pal); break;
    case 'tonnendach': case 'bogendach': base = svgTonneBogen(pal); break;
    case 'schmetterling': base = svgSchmetterling(pal); break;
    case 'grabendach': base = svgGraben(pal); break;
    case 'halle': base = svgHalle(pal); break;
    case 'l-shape': base = svgGrundriss('l', pal); break;
    case 't-shape': base = svgGrundriss('t', pal); break;
    case 'u-grundriss': base = svgGrundriss('u', pal); break;
    case 'mehrfluegel': base = svgMehrfluegel(pal); break;
    case 'mehrkoerper': base = svgMehrkoerper(pal); break;
    default: base = svgSattel(pal);
  }

  // PV-Felder (auf die Dachfläche geclippt -> kein „Schweben") und Aufbauten getrennt.
  const overlaysPv: string[] = [];       // PV-Belegung (separater Planungsschritt, solide)
  const overlaysAufbau: string[] = [];   // Aufbauten, die „Anwenden" automatisch setzt (solide)
  const overlaysVorschau: string[] = []; // Aufbauten OHNE Obstacle-Typ (Lichtkuppel/Schneefang) -> gestrichelt
  const istPult = k === 'pult' || k === 'schleppdach' || k === 'versetztes-pult';

  // PV-Module — nur bei echtem PV-Token, nicht auf reinen Grundriss-Andeutungen.
  if ((hasTok('pv') || hasTok('photovoltaik')) && !isGrund) {
    let px = 72; let py = 34; let cols = 3; let rows = 2;
    if (istPult) { px = 40; py = 40; cols = 4; rows = 1; }
    else if (k === 'walm') { px = 56; py = 36; cols = 3; rows = 2; }
    else if (isFlat) { px = 30; py = 41; cols = 4; rows = 1; }
    else if (k === 'mehrkoerper') { px = 26; py = 38; cols = 3; rows = 1; }
    overlaysPv.push(svgPvFeld(px, py, cols, rows, pal));
  }

  if (hasTok('kamin') || hasTok('schornstein')) overlaysAufbau.push(svgKamin(isFlat ? 70 : 56, isFlat ? 50 : 34, pal));
  if (hasTok('lüfter') || hasTok('luefter') || hasTok('rauchabzug') || hasTok('entlüftung')) {
    overlaysAufbau.push(svgLuefter(isFlat ? 44 : 84, isFlat ? 42 : 32, pal));
    overlaysAufbau.push(svgLuefter(isFlat ? 58 : 92, isFlat ? 42 : 34, pal));
  }
  if (hasTok('sat') || hasTok('satellit')) overlaysAufbau.push(svgSat(96, 30, pal));
  // Lichtkuppel/Oberlicht = eigene Aufbauart, Schneefang = linienförmiges Dachbauteil -> beide
  // werden gesetzt und daher SOLIDE dargestellt (kein gestrichelter Vorschau-Rest mehr).
  if (hasTok('lichtkuppel') || hasTok('oberlicht')) overlaysAufbau.push(svgLichtkuppel(54, 42, pal));
  if (hasTok('schneefang')) overlaysAufbau.push(svgSchneefang(pal));

  if (hasTok('dachfenster')) {
    if (istPult) {
      // Pult: Fenster der flacheren Schräge folgen lassen (kein Schweben über der Fläche).
      overlaysAufbau.push(svgDachfenster(40, 43, pal), svgDachfenster(70, 47, pal));
    } else if (hasTok('fenster4')) {
      overlaysAufbau.push(svgDachfenster(36, 38, pal), svgDachfenster(52, 38, pal), svgDachfenster(72, 38, pal), svgDachfenster(88, 38, pal));
    } else {
      overlaysAufbau.push(svgDachfenster(46, 38, pal), svgDachfenster(74, 38, pal));
    }
  }

  if (hasTok('gaube')) {
    // Gaubenart aus Schlagworten -> distinkte Silhouette (Titel = Bild). Spitz/Tonne/Fledermaus/
    // Rund/Zwerch sind als Vorschau unterscheidbar (auch wenn nur Schlepp/Giebel/Walm/Flach als
    // Obstacle gesetzt werden -> Status 'geplant' für die übrigen Arten).
    const typ: GaubeSvgTyp =
      hasTok('flachgaube') || hasTok('flachdachgaube') ? 'flach'
        : (hasTok('schleppgaube') || hasTok('schlepp')) ? 'schlepp'
          : (hasTok('walmgaube') || hasTok('walmdachgaube')) ? 'walm'
            : (hasTok('spitzgaube') || hasTok('dreiecksgaube') || hasTok('spitz') || hasTok('dreieck')) ? 'spitz'
              : (hasTok('tonnengaube') || hasTok('segmentbogengaube') || hasTok('bogengaube') || hasTok('tonne') || hasTok('segmentbogen')) ? 'tonne'
                : (hasTok('fledermausgaube') || hasTok('fledermaus')) ? 'fledermaus'
                  : (hasTok('rundgaube') || hasTok('rund')) ? 'rund'
                    : (hasTok('zwerchgaube') || hasTok('zwerch')) ? 'zwerch'
                      : 'giebel'; // satteldachgaube/giebelgaube/generisch
    if (hasTok('gaubenband') || hasTok('band')) {
      overlaysAufbau.push(svgGaube(28, 40, 13, typ, pal), svgGaube(45, 40, 13, typ, pal), svgGaube(62, 40, 13, typ, pal), svgGaube(79, 40, 13, typ, pal));
    } else if (hasTok('drei')) {
      overlaysAufbau.push(svgGaube(30, 40, 14, typ, pal), svgGaube(58, 40, 14, typ, pal), svgGaube(86, 40, 14, typ, pal));
    } else if (hasTok('mehrere') || hasTok('kleine')) {
      overlaysAufbau.push(svgGaube(38, 40, 16, typ, pal), svgGaube(78, 40, 16, typ, pal));
    } else if (hasTok('breit') || hasTok('mittelgaube')) {
      overlaysAufbau.push(svgGaube(46, 40, 40, typ, pal));
    } else if (hasTok('zwei')) {
      overlaysAufbau.push(svgGaube(40, 40, 18, typ, pal), svgGaube(76, 40, 18, typ, pal));
    } else {
      overlaysAufbau.push(svgGaube(54, 40, 24, typ, pal));
    }
  }

  // Garage/Carport als eigenständigen kleinen Baukörper kenntlich machen (Tor bzw. offene Stützen).
  // Solide Struktur (Teil des Gebäudes), kein gestrichelter Aufbau. Nicht bei Mehrkörper/Grundriss.
  const strukturOverlays: string[] = [];
  if (!isGrund && k !== 'mehrkoerper') {
    if (hasTok('carport')) strukturOverlays.push(svgCarportOffen(66, 84, pal));
    else if (hasTok('garage')) strukturOverlays.push(svgGarageTor(66, 84, pal));
  }
  const strukturGruppe = strukturOverlays.join('');

  // PV-Felder auf die Dachfläche clippen (Sattel/Pult/Walm/Flach) -> Module ragen nie in den „Himmel".
  const clipPts = roofClipPts(k);
  const clipId = 'rc' + v.id.replace(/[^a-zA-Z0-9]/g, '');
  const clipDef = clipPts
    ? `<clipPath id="${clipId}"><polygon points="${clipPts.map(([x, y]) => `${svgN(x)},${svgN(y)}`).join(' ')}"/></clipPath>`
    : '';
  const pvGruppe = overlaysPv.length
    ? (clipPts ? `<g clip-path="url(#${clipId})">${overlaysPv.join('')}</g>` : overlaysPv.join(''))
    : '';

  // Auto-gesetzte Aufbauten (Kamin/Dachfenster/Lüfter/Sat/Gaube) werden durch „Anwenden" real
  // platziert -> SOLIDE darstellen. Nur nicht-setzbare Merkmale (Lichtkuppel/Schneefang) bleiben
  // Vorschau -> bei verfügbaren Vorlagen gestrichelt + transparent kennzeichnen.
  const aufbauGruppe = overlaysAufbau.join('');
  const vorschauGruppe = overlaysVorschau.length
    ? (v.status === 'verfuegbar'
        ? `<g opacity="0.5" stroke-dasharray="2.5 2">${overlaysVorschau.join('')}</g>`
        : overlaysVorschau.join(''))
    : '';

  const hintergrund = geplant ? '#f8fafc' : '#ffffff';
  return `<svg viewBox="0 0 ${SVG_W} ${SVG_H}" width="100%" height="100%" preserveAspectRatio="xMidYMid meet" role="img" aria-label="${svgEsc(v.name)}" xmlns="http://www.w3.org/2000/svg"><rect x="0" y="0" width="${SVG_W}" height="${SVG_H}" fill="${hintergrund}"/>${clipDef}<g>${base}${strukturGruppe}${pvGruppe}${aufbauGruppe}${vorschauGruppe}</g></svg>`;
}

// =====================================================================
// 5. APPLY (rein — gibt nur Daten zurück, KEIN Engine-/State-Zugriff)
// =====================================================================

/**
 * Liefert den additiv gemergten Param-Satz für setBuild (NUR Geometrie).
 * Korrektur (deckungsneutral): liefert KEINE cover/Dacheindeckung — die Materialauswahl
 * bleibt unangetastet und wird ausschließlich über die separate Produktauswahl gesetzt.
 * - status 'geplant' (oder nicht sauber baubar) → { ok:false, grund } (kein build).
 * - 'verfuegbar' → { ok:true, build:{ ...prevBuild, ...apply.build }, warnungen }.
 * Einheiten strikt: m / cm / Grad 1:1; unberührte Felder (lengthB/widthB/layerSpread)
 * behalten den bisherigen Wert.
 */
export function applyVorlage(v: DachformVorlage, prevBuild: BuildingParamsLike): VorlagenApplyResult {
  if (!istAnwendbar(v) || !v.apply) {
    return {
      ok: false,
      grund: v.geplantGrund ?? 'Diese Dachform ist in der aktuellen Engine noch nicht sauber baubar (geplant).',
      warnungen: [],
    };
  }
  const build: BuildingParamsLike = { ...prevBuild, ...v.apply.build };
  const warnungen = validateVorlage(v).warnungen;
  // NUR Geometrie (build) + sicher setzbare Standard-Aufbauten, FLÄCHENABHÄNGIG platziert
  // (Maße/Position an die echte Zielfläche gekoppelt). KEIN cover -> Dacheindeckung/Material
  // bleibt unverändert (separate Produktauswahl).
  const surfaceId = hauptflaecheId(v);
  const flaeche = surfaceId ? hauptflaecheInfo(build, surfaceId) : null;
  const wuensche = standardAufbauten(v);
  const aufbauten: PlatzierterAufbau[] = (wuensche.length > 0 && surfaceId && flaeche)
    ? platziereAufbauten(wuensche, flaeche)
    : [];
  // Linienbauteile (Schneefang): flächenabhängig parallel zur Traufe, PV-Sperrzone vorbereitet.
  const s = new Set(v.schlagworte.map((x) => x.toLowerCase()));
  const linienBauteile: DachLinienBauteil[] = [];
  if (surfaceId && flaeche && s.has('schneefang')) {
    const erg = platziereSchneefang(surfaceId, flaeche);
    if (erg.bauteil) linienBauteile.push(erg.bauteil);
  }
  return { ok: true, build, warnungen, aufbauten, linienBauteile };
}

/**
 * Geometrie der Haupt-Dachfläche für die Aufbauplatzierung — REIN aus den Build-Parametern
 * abgeleitet (passend zu buildSattel/Pult/Walm/Flat; konservativ). So ist die Platzierung ohne
 * Engine-Timing möglich. u-Breite = surf.width (Bounding-Box), v-Höhe = geneigte Slope (surf.height).
 * Die Walm-Südfläche ist trapezförmig (verjüngt zur First: breiteFirstM = Firstlänge L−W).
 */
export function hauptflaecheInfo(build: BuildingParamsLike, surfaceId: string): DachflaecheInfo {
  const L = Math.max(0.1, build.length);
  const B = Math.max(0.1, build.width);
  const oh = Math.max(0, build.overhang ?? 0);
  const ohG = Math.max(0, build.overhangGable ?? 0);
  if (build.category === 'flat') {
    return { surfaceId, breiteTraufeM: L, breiteFirstM: L, hoeheM: B, form: 'rechteck' };
  }
  if (build.shape === 'pult') {
    return { surfaceId, breiteTraufeM: L + 2 * ohG, breiteFirstM: L + 2 * ohG, hoeheM: pultSparrenlaengeM(B, oh, build.pitch), form: 'rechteck' };
  }
  if (build.shape === 'walm') {
    const wTop = Math.max(0, L - B); // Firstlänge = wTop des Süd-Trapezes
    return { surfaceId, breiteTraufeM: L + 2 * oh, breiteFirstM: wTop, hoeheM: sattelSparrenlaengeM(B, oh, build.pitch), form: wTop <= 0.05 ? 'dreieck' : 'trapez' };
  }
  // sattel (und Fallback): rechteckige Hauptfläche
  return { surfaceId, breiteTraufeM: L + 2 * ohG, breiteFirstM: L + 2 * ohG, hoeheM: sattelSparrenlaengeM(B, oh, build.pitch), form: 'rechteck' };
}

// =====================================================================
// 6. RICHTWERT-BAUSTEINE (konfigurierbar, KEINE harten Normwerte)
// =====================================================================

export const HINWEIS_STATIK =
  'Alle Holzquerschnitte und Abstände sind Richtwerte. Die statische Bemessung ' +
  '(Schnee-/Wind-/Nutzlasten, Verbindungsmittel, Aussteifung) ist separat durch eine fachkundige Person ' +
  '(Tragwerksplaner/Statiker) nachzuweisen.';

// Korrektur: einheitlicher, deckungsneutraler Hinweis für JEDE Vorlage. Dachform-Vorlagen
// setzen NIE ein Deckmaterial; Regeldachneigung, Zusatzmaßnahmen, Lattmaß und Anschlussdetails
// sind abhängig vom später separat gewählten Produkt zu prüfen.
export const DECKUNG_HINWEIS =
  'Dachdeckung separat auswählen. Regeldachneigung, Zusatzmaßnahmen, Lattmaß und Anschlussdetails ' +
  'sind abhängig vom später über die Produktauswahl gewählten Produkt zu prüfen.';

const STATIK_PV_HINWEIS =
  'Belegung, Sperrzonen und Reihenabstände sind Plausibilitäts-Richtwerte. Die statische Eignung der ' +
  'Unterkonstruktion und der Lasteintrag in das Tragwerk sind separat durch eine fachkundige Person nachzuweisen.';

const FLAGS_SATTEL: ZimmererFlags = {
  sparren: true, firstpfette: true, mittelpfette: false, fusspfette: true,
  kehlbalken: true, stuhlsaeule: false, strebeKopfband: false, zange: true,
  aufschiebling: true, gratsparren: false, kehlsparren: false, schifter: false, wechsel: true,
};
const FLAGS_SATTEL_STEIL: ZimmererFlags = {
  ...FLAGS_SATTEL, mittelpfette: true, stuhlsaeule: true, strebeKopfband: true,
};
const FLAGS_PULT: ZimmererFlags = {
  sparren: true, firstpfette: true, mittelpfette: false, fusspfette: true,
  kehlbalken: false, stuhlsaeule: false, strebeKopfband: false, zange: false,
  aufschiebling: true, gratsparren: false, kehlsparren: false, schifter: false, wechsel: true,
};
const FLAGS_WALM: ZimmererFlags = {
  sparren: true, firstpfette: true, mittelpfette: true, fusspfette: true,
  kehlbalken: false, stuhlsaeule: true, strebeKopfband: true, zange: false,
  aufschiebling: true, gratsparren: true, kehlsparren: false, schifter: true, wechsel: true,
};
const FLAGS_FLACH: ZimmererFlags = {
  sparren: false, firstpfette: false, mittelpfette: false, fusspfette: false,
  kehlbalken: false, stuhlsaeule: false, strebeKopfband: false, zange: false,
  aufschiebling: false, gratsparren: false, kehlsparren: false, schifter: false, wechsel: false,
};
const FLAGS_GEPLANT: ZimmererFlags = {
  sparren: true, firstpfette: false, mittelpfette: false, fusspfette: true,
  kehlbalken: false, stuhlsaeule: false, strebeKopfband: false, zange: false,
  aufschiebling: false, gratsparren: false, kehlsparren: false, schifter: false, wechsel: false,
};
// Bogen-/Tonnendach: Tragglied ist der Bogenbinder (BSH), KEINE ebenen Sparren.
const FLAGS_BOGEN: ZimmererFlags = {
  sparren: false, firstpfette: false, mittelpfette: false, fusspfette: true,
  kehlbalken: false, stuhlsaeule: false, strebeKopfband: false, zange: false,
  aufschiebling: false, gratsparren: false, kehlsparren: false, schifter: false, wechsel: false,
};

function pitchedDachdecker(over: Partial<VorlagenDachdecker> & { empfohleneEindeckung?: RoofCovering; zulaessigeEindeckungen?: RoofCovering[] }): VorlagenDachdecker {
  // Korrektur: etwaige Eindeckungs-Inputs werden IGNORIERT (deckungsneutral, nie ausgegeben).
  const { empfohleneEindeckung, zulaessigeEindeckungen, ...rest } = over;
  void empfohleneEindeckung; void zulaessigeEindeckungen;
  return {
    deckungsHinweis: DECKUNG_HINWEIS,
    dachdeckungSeparatAuswaehlen: true,
    regeldachneigungAbhaengigVonMaterial: true,
    lattmassAbhaengigVonProdukt: true,
    rdnGrad: 22,
    mindestneigungGrad: 16,
    battenDistCm: 34,
    konterlattungMm: [24, 48],
    unterdeckungKlasse: 'Geneigtes Dach: Unterdeckung/Zusatzmaßnahme abhängig vom später gewählten Produkt (Richtwert)',
    firstausbildung: 'Belüfteter First (Detailausbildung produktabhängig)',
    // gratausbildung bewusst NICHT als Default: Sattel-/Pultdach haben keinen Grat.
    // Nur Formen mit Graten (Walm/Krüppelwalm) setzen gratausbildung explizit.
    ortgangausbildung: 'Ortgangabschluss (Ausführung produktabhängig)',
    traufausbildung: 'Traufblech + Lüftungselement + Rinneneinhang (Richtwert)',
    entwaesserungHinweis: 'Vorgehängte Rinne + Fallrohr, Bemessung nach Dachfläche (Richtwert)',
    schneefangHinweis: 'Schneefang traufseitig nach Schneelastzone (Richtwert)',
    lueftungHinweis: 'Hinterlüftung First/Traufe ≥ 2 cm freier Querschnitt (Richtwert)',
    ...rest,
  };
}

function flatDachdecker(over: Partial<VorlagenDachdecker> & { empfohleneEindeckung?: RoofCovering; zulaessigeEindeckungen?: RoofCovering[] }): VorlagenDachdecker {
  // Korrektur: etwaige Eindeckungs-Inputs werden IGNORIERT (deckungsneutral, nie ausgegeben).
  const { empfohleneEindeckung, zulaessigeEindeckungen, ...rest } = over;
  void empfohleneEindeckung; void zulaessigeEindeckungen;
  return {
    deckungsHinweis: DECKUNG_HINWEIS,
    dachdeckungSeparatAuswaehlen: true,
    regeldachneigungAbhaengigVonMaterial: true,
    lattmassAbhaengigVonProdukt: true,
    rdnGrad: 2,
    mindestneigungGrad: 1.5,
    battenDistCm: 0,
    konterlattungMm: [0, 0],
    unterdeckungKlasse: 'Flachdach: Abdichtungsaufbau (Warm-/Kaltdach) abhängig vom später gewählten Produkt (Richtwert)',
    firstausbildung: 'entfällt (Flachdach)',
    ortgangausbildung: 'Attika-Abdeckung mit Tropfkante',
    traufausbildung: 'Attika/Kiesfangleiste + innen- oder außenliegende Entwässerung',
    entwaesserungHinweis: 'Gefälledämmung ≥ 2 % (Richtwert), Notüberläufe vorsehen',
    schneefangHinweis: 'entfällt',
    lueftungHinweis: 'Warmdach unbelüftet; Kaltdach nur mit definierter Belüftungsebene',
    ...rest,
  };
}

function pitchedPv(over: Partial<VorlagenPv>): VorlagenPv {
  return {
    belegbareSeiten: ['Hauptdach (Sonnenseite)'],
    ausgeschlosseneSeiten: ['Nordhang'],
    sperrzoneFirstM: 0.5,
    sperrzoneTraufeM: 0.4,
    sperrzoneOrtgangM: 0.5,
    empfohleneAusrichtung: 'portrait',
    marginCm: 40,
    ukTyp: 'Aufdach-Schienensystem mit Dachhaken',
    statikPlausibilitaetHinweis: STATIK_PV_HINWEIS,
    ...over,
  };
}

function flatPv(over: Partial<VorlagenPv>): VorlagenPv {
  return {
    belegbareSeiten: ['Dachfläche aufgeständert (Süd oder Ost-West)'],
    ausgeschlosseneSeiten: ['Rand-/Eckzonen (Windsog)'],
    sperrzoneFirstM: 0,
    sperrzoneTraufeM: 0.5,
    sperrzoneOrtgangM: 0.5,
    empfohleneAusrichtung: 'landscape',
    marginCm: 50,
    ukTyp: 'Ballastiertes Aufständerungssystem (Ost-West/Süd)',
    flachdachReihenabstandGeplant: true,
    statikPlausibilitaetHinweis: STATIK_PV_HINWEIS,
    ...over,
  };
}

// =====================================================================
// 7. VORLAGEN-DATEN
// =====================================================================

// =====================================================================
// 6b. KOMPAKTE BAUSTEINE für die erweiterte Bibliothek (verfügbar)
// =====================================================================
// Diese Helfer erzeugen vollständige, voll typisierte DachformVorlage-Objekte
// für die zusätzlichen verfügbaren Vorlagen. Sie nutzen ausschließlich die
// bereits vorhandenen Richtwert-Bausteine (pitchedDachdecker/flatDachdecker/
// pitchedPv/flatPv/FLAGS_*). Die bestehenden 25 Vorlagen bleiben unberührt.

interface VerfSpec {
  id: string;
  name: string;
  kurz: string;
  schlagworte: string[];
  length: number;
  width: number;
  height: number;
  pitch: number;
  /** @deprecated Deckungsneutral: wird IGNORIERT (kein setCover). Material nur über Produktauswahl. */
  cover?: RoofCovering;
  overhang?: number;
  overhangGable?: number;
  battenDist?: number;
  rafterSpacing?: number;
  rafterWidth?: number;
  rafterHeight?: number;
  attika?: number;
  flags?: ZimmererFlags;
  dachstuhltyp?: string;
  dd?: Partial<VorlagenDachdecker>;
  pv?: Partial<VorlagenPv>;
}

function verfPitched(
  shape: 'sattel' | 'pult' | 'walm',
  s: VerfSpec,
  extra: Partial<VorlagenGeometrie>,
  flagsDefault: ZimmererFlags,
  dachstuhlDefault: string,
): DachformVorlage {
  // Korrektur: kein cover mehr ableiten — Dachform-Vorlage ist deckungsneutral.
  const overhang = s.overhang ?? 0.5;
  const overhangGable = s.overhangGable ?? 0.3;
  const battenDist = s.battenDist ?? 34;
  const rafterSpacing = s.rafterSpacing ?? 70;
  const rafterWidth = s.rafterWidth ?? 8;
  const rafterHeight = s.rafterHeight ?? 18;
  return {
    id: s.id,
    name: s.name,
    kurzbeschreibung: s.kurz,
    status: 'verfuegbar',
    anwendbar: true,
    schlagworte: s.schlagworte,
    geometrie: {
      ...extra,
      category: 'pitched',
      engineShape: shape,
      shapeKey: shape,
      defaultLength: s.length,
      defaultWidth: s.width,
      defaultHeight: s.height,
      defaultPitch: s.pitch,
      defaultOverhang: overhang,
      defaultOverhangGable: overhangGable,
    },
    zimmerer: {
      dachstuhltyp: s.dachstuhltyp ?? dachstuhlDefault,
      flags: s.flags ?? flagsDefault,
      querschnittSparrenCm: [rafterWidth, rafterHeight],
      querschnittPfetteCm: [14, 14],
      materialFestigkeit: 'NH C24 (Richtwert)',
      holzfeuchteProzent: '≤ 20 % (KVH ≤ 15 %)',
      sparrenabstandCm: rafterSpacing,
      abbundhinweis: 'Sparren auf Pfetten abgebunden; Aufschiebling am Traufüberstand (Richtwert).',
      spannweiteHinweis: 'Sparrenspannweite/Querschnitt statisch zu prüfen (Richtwert).',
      lastabtragsweg: 'Sparren → Pfetten → tragende Wände → Ringanker.',
    },
    dachdecker: pitchedDachdecker({ ...s.dd, battenDistCm: battenDist }),
    pv: pitchedPv(s.pv ?? {}),
    apply: {
      build: {
        category: 'pitched',
        shape,
        length: s.length,
        width: s.width,
        height: s.height,
        pitch: s.pitch,
        overhang,
        overhangGable,
        rafterSpacing,
        rafterWidth,
        rafterHeight,
        battenDist,
      },
    },
    hinweisStatik: HINWEIS_STATIK,
  };
}

function verfSattel(s: VerfSpec): DachformVorlage {
  return verfPitched('sattel', s, { symmetrie: 'symmetrisch' }, FLAGS_SATTEL, 'Pfettendach (Satteldach, Richtwert)');
}
function verfPult(s: VerfSpec): DachformVorlage {
  return verfPitched('pult', s, { gefaelleRichtung: 'breite', symmetrie: 'asymmetrisch' }, FLAGS_PULT, 'Pultdach (durchlaufende Sparren, Trauf-/Firstpfette)');
}
function verfWalm(s: VerfSpec): DachformVorlage {
  return verfPitched('walm', s, { symmetrie: 'symmetrisch', benoetigtLaengerGleichBreite: true }, FLAGS_WALM, 'Walmdach (Pfettendach mit Gratsparren/Schifter/Stuhl)');
}
function verfFlach(s: VerfSpec): DachformVorlage {
  // Korrektur: kein cover mehr ableiten — Dachform-Vorlage ist deckungsneutral.
  const battenDist = s.battenDist ?? 0;
  const rafterSpacing = s.rafterSpacing ?? 62.5;
  const rafterWidth = s.rafterWidth ?? 10;
  const rafterHeight = s.rafterHeight ?? 22;
  const attika = s.attika ?? 0.3;
  return {
    id: s.id,
    name: s.name,
    kurzbeschreibung: s.kurz,
    status: 'verfuegbar',
    anwendbar: true,
    schlagworte: s.schlagworte,
    geometrie: {
      category: 'flat',
      engineShape: 'rect',
      shapeKey: 'rect',
      defaultLength: s.length,
      defaultWidth: s.width,
      defaultHeight: s.height,
      defaultPitch: s.pitch,
      defaultOverhang: s.overhang ?? 0,
      defaultOverhangGable: s.overhangGable ?? 0,
      symmetrie: 'symmetrisch',
    },
    zimmerer: {
      dachstuhltyp: s.dachstuhltyp ?? 'Tragdecke mit Gefälledämmung (Flachdach, Richtwert)',
      flags: s.flags ?? FLAGS_FLACH,
      querschnittSparrenCm: [rafterWidth, rafterHeight],
      materialFestigkeit: 'NH C24 / Stahlbeton (je Konstruktion)',
      holzfeuchteProzent: '≤ 18 % (Holzdecke)',
      sparrenabstandCm: rafterSpacing,
      abbundhinweis: 'Tragdecke mit Gefälledämmung; kein Sparrendach.',
      spannweiteHinweis: 'Durchbiegung/Pfützenbildung beachten; Gefälle ≥ 2 % (Richtwert).',
      lastabtragsweg: 'Abdichtung → Dämmung → Tragdecke → Wände.',
    },
    dachdecker: flatDachdecker({ ...s.dd }),
    pv: flatPv(s.pv ?? {}),
    apply: {
      build: {
        category: 'flat',
        shape: 'rect',
        length: s.length,
        width: s.width,
        height: s.height,
        pitch: s.pitch,
        overhang: s.overhang ?? 0,
        overhangGable: s.overhangGable ?? 0,
        rafterSpacing,
        rafterWidth,
        rafterHeight,
        battenDist,
        attika,
      },
    },
    hinweisStatik: HINWEIS_STATIK,
  };
}

// Eingabeaufforderung 13: VERFÜGBARES Flachdach auf zusammengesetztem Grundriss (L/T/U).
// buildFlat erzeugt EIN echtes Grundrisspolygon (keine Doppelzählung, keine Rechteck-Ersatzform);
// die Dachfläche 'main' existiert real. Deckungsneutral; keine Aufbauten-Auto-Platzierung (Innenwinkel).
interface FlachGrundrissSpec {
  id: string; name: string; kurz: string; schlagworte: string[];
  shapeKey: 'l-shape' | 't-shape' | 'u-grundriss';
  engineShape: 'l-shape' | 't-shape' | 'u-shape';
  length: number; width: number; lengthB: number; widthB: number; height: number;
  innenwinkel: number; innenhof: boolean; kehleHinweis: string;
  dachstuhltyp?: string; attika?: number;
}
function verfFlachGrundriss(s: FlachGrundrissSpec): DachformVorlage {
  const attika = s.attika ?? 0.3;
  return {
    id: s.id,
    name: s.name,
    kurzbeschreibung: s.kurz,
    status: 'verfuegbar',
    anwendbar: true,
    schlagworte: s.schlagworte,
    geometrie: {
      category: 'flat',
      engineShape: s.engineShape,
      shapeKey: s.shapeKey,
      defaultLength: s.length,
      defaultWidth: s.width,
      defaultHeight: s.height,
      defaultPitch: 3,
      defaultOverhang: 0,
      defaultOverhangGable: 0,
      symmetrie: s.shapeKey === 'l-shape' ? 'asymmetrisch' : 'symmetrisch',
    },
    zimmerer: {
      dachstuhltyp: s.dachstuhltyp ?? `Tragdecke mit Gefälledämmung, zusammengesetzter ${s.shapeKey === 'l-shape' ? 'L' : s.shapeKey === 't-shape' ? 'T' : 'U'}-Grundriss (Flachdach)`,
      flags: FLAGS_FLACH,
      querschnittSparrenCm: [10, 22],
      materialFestigkeit: 'NH C24 / Stahlbeton (je Konstruktion)',
      holzfeuchteProzent: '≤ 18 % (Holzdecke)',
      sparrenabstandCm: 62.5,
      abbundhinweis: 'Tragdecke mit Gefälledämmung; kein Sparrendach. Entwässerung der einspringenden Bereiche beachten.',
      spannweiteHinweis: `${s.innenwinkel} einspringende${s.innenwinkel === 1 ? 'r' : ''} Innenwinkel${s.innenhof ? ' + Innenhof' : ''}; Durchbiegung/Pfützenbildung beachten (Richtwert).`,
      lastabtragsweg: 'Abdichtung → Dämmung → Tragdecke → Wände (mehrere Baukörper).',
    },
    dachdecker: flatDachdecker({
      unterdeckungKlasse: 'Flachdach (Warm-/Kaltdach) abhängig vom später gewählten Produkt (Richtwert)',
      entwaesserungHinweis: s.kehleHinweis,
    }),
    pv: flatPv({ belegbareSeiten: ['Flachdach-Teilflächen je Gebäudeflügel'], ausgeschlosseneSeiten: ['Innenwinkel/Kehlbereiche', 'Rand-/Eckzonen (Windsog)'] }),
    apply: {
      build: {
        category: 'flat',
        shape: s.engineShape,
        length: s.length,
        width: s.width,
        height: s.height,
        pitch: 3,
        overhang: 0,
        overhangGable: 0,
        lengthB: s.lengthB,
        widthB: s.widthB,
        rafterSpacing: 62.5,
        rafterWidth: 10,
        rafterHeight: 22,
        battenDist: 0,
        attika,
      },
    },
    hinweisStatik:
      'Zusammengesetzter Grundriss geometrisch ermittelt (echtes Polygon, keine Doppelzählung). ' +
      'Dachverschneidungen, Kehlen, Entwässerung und statische Auslegung fachlich prüfen.',
  };
}

const VERFUEGBAR: DachformVorlage[] = [
  {
    id: 'sattel-standard',
    name: 'Satteldach Standard (EFH)',
    kurzbeschreibung: 'Klassisches symmetrisches Satteldach für Einfamilienhäuser. Südhang voll belegbar, Nordhang ausgeschlossen.',
    status: 'verfuegbar',
    anwendbar: true,
    schlagworte: ['satteldach', 'sattel', 'efh', 'ziegel', 'standard', 'schrägdach'],
    geometrie: {
      category: 'pitched', engineShape: 'sattel', shapeKey: 'sattel',
      defaultLength: 10, defaultWidth: 8, defaultHeight: 5, defaultPitch: 35,
      defaultOverhang: 0.5, defaultOverhangGable: 0.3, symmetrie: 'symmetrisch',
    },
    zimmerer: {
      dachstuhltyp: 'Pfettendach (First- und Fußpfetten, Zangen)',
      flags: FLAGS_SATTEL,
      querschnittSparrenCm: [8, 18],
      querschnittPfetteCm: [14, 14],
      materialFestigkeit: 'NH C24 (Konstruktionsvollholz, Richtwert)',
      holzfeuchteProzent: '≤ 20 % (KVH ≤ 15 %)',
      sparrenabstandCm: 70,
      abbundhinweis: 'Sparren auf First-/Fußpfette geklinkt (Kerve), Aufschiebling für Traufüberstand.',
      spannweiteHinweis: 'Sparrenspannweite bei 8/18 und e=70 cm ca. bis 4,5 m frei (Richtwert, statisch zu prüfen).',
      lastabtragsweg: 'Sparren → Pfetten → Stützen/Außenwand → Ringanker.',
    },
    dachdecker: pitchedDachdecker({ empfohleneEindeckung: 'ziegel', rdnGrad: 22, mindestneigungGrad: 16 }),
    pv: pitchedPv({ belegbareSeiten: ['Hauptdach Süd (main_S)'], ausgeschlosseneSeiten: ['Nordhang (main_N)'] }),
    apply: {
      build: {
        category: 'pitched', shape: 'sattel', length: 10, width: 8, height: 5, pitch: 35,
        overhang: 0.5, overhangGable: 0.3, rafterSpacing: 70, rafterWidth: 8, rafterHeight: 18, battenDist: 34,
      },
    },
    hinweisStatik: HINWEIS_STATIK,
  },
  {
    id: 'sattel-schiefer-steil',
    name: 'Steiles Satteldach (Schiefer)',
    kurzbeschreibung: 'Steil geneigtes Satteldach mit Schiefer-Doppeldeckung. Ost-West oder Süd belegbar, Nordhang ausgeschlossen.',
    status: 'verfuegbar',
    anwendbar: true,
    schlagworte: ['satteldach', 'sattel', 'schiefer', 'steil', 'doppeldeckung', 'schrägdach'],
    geometrie: {
      category: 'pitched', engineShape: 'sattel', shapeKey: 'sattel',
      defaultLength: 10, defaultWidth: 8, defaultHeight: 5, defaultPitch: 45,
      defaultOverhang: 0.5, defaultOverhangGable: 0.3, symmetrie: 'symmetrisch',
    },
    zimmerer: {
      dachstuhltyp: 'Pfettendach mit Mittelpfette und Stuhl (steile Neigung)',
      flags: FLAGS_SATTEL_STEIL,
      querschnittSparrenCm: [8, 18],
      querschnittPfetteCm: [16, 16],
      materialFestigkeit: 'NH C24 (Richtwert)',
      holzfeuchteProzent: '≤ 20 % (KVH ≤ 15 %)',
      sparrenabstandCm: 70,
      abbundhinweis: 'Steile Neigung → höhere Längskräfte; Stuhl/Streben zur Aussteifung.',
      spannweiteHinweis: 'Bei 45° wirkt mehr Eigenlast über die Schiefer-Doppeldeckung (Richtwert).',
      lastabtragsweg: 'Sparren → Mittel-/Firstpfette → Stuhlsäulen → Decke.',
    },
    dachdecker: pitchedDachdecker({
      empfohleneEindeckung: 'schiefer', rdnGrad: 25, mindestneigungGrad: 22, battenDistCm: 30,
      firstausbildung: 'Schiefer-First (gedeckt) bzw. Metallfirst',
    }),
    pv: pitchedPv({ belegbareSeiten: ['Hauptdach Süd', 'Hauptdach Ost/West'], ausgeschlosseneSeiten: ['Nordhang'] }),
    apply: {
      build: {
        category: 'pitched', shape: 'sattel', length: 10, width: 8, height: 5, pitch: 45,
        overhang: 0.5, overhangGable: 0.3, rafterSpacing: 70, rafterWidth: 8, rafterHeight: 18, battenDist: 30,
      },
    },
    hinweisStatik: HINWEIS_STATIK,
  },
  {
    id: 'sattel-blech-flachgeneigt',
    name: 'Flachgeneigtes Satteldach (Trapezblech)',
    kurzbeschreibung: 'Flachgeneigtes Satteldach mit Trapezblech. 15° liegt unter der hier angesetzten Regeldachneigung → regensicheres Unterdach / dichtgelegte Überdeckung als Zusatzmaßnahme.',
    status: 'verfuegbar',
    anwendbar: true,
    schlagworte: ['satteldach', 'sattel', 'trapezblech', 'flachgeneigt', 'blech', 'stehfalz', 'schrägdach'],
    geometrie: {
      category: 'pitched', engineShape: 'sattel', shapeKey: 'sattel',
      defaultLength: 12, defaultWidth: 8, defaultHeight: 4, defaultPitch: 15,
      defaultOverhang: 0.4, defaultOverhangGable: 0.3, symmetrie: 'symmetrisch',
    },
    zimmerer: {
      dachstuhltyp: 'Pfettendach (flach geneigt, durchlaufende Pfetten)',
      flags: FLAGS_SATTEL,
      querschnittSparrenCm: [8, 18],
      querschnittPfetteCm: [14, 16],
      materialFestigkeit: 'NH C24 (Richtwert)',
      holzfeuchteProzent: '≤ 20 % (KVH ≤ 15 %)',
      sparrenabstandCm: 70,
      abbundhinweis: 'Trapezblech-Auflager: Pfettenabstand auf Profil-Tragweite abstimmen.',
      spannweiteHinweis: 'Geringe Neigung → größere wirksame Schneelast-Komponente (Richtwert).',
      lastabtragsweg: 'Profiltafel → Pfetten/Sparren → Außenwand.',
    },
    dachdecker: pitchedDachdecker({
      empfohleneEindeckung: 'trapezblech',
      rdnGrad: 18, mindestneigungGrad: 5, battenDistCm: 40,
      unterdeckungKlasse: 'Regensicheres Unterdach erforderlich (Neigung < RDN, Richtwert)',
      firstausbildung: 'Firstblech mit Dichtprofil',
      lueftungHinweis: 'Belüftete Konterlattungsebene unter dem Profil (Richtwert)',
    }),
    pv: pitchedPv({
      belegbareSeiten: ['Hauptdach Süd', 'Hauptdach Nord (flach → Ost-West möglich)'],
      ausgeschlosseneSeiten: [],
      empfohleneAusrichtung: 'landscape',
      ukTyp: 'Stehfalz-/Trapez-Klemmen (durchdringungsfrei)',
    }),
    apply: {
      build: {
        category: 'pitched', shape: 'sattel', length: 12, width: 8, height: 4, pitch: 15,
        overhang: 0.4, overhangGable: 0.3, rafterSpacing: 70, rafterWidth: 8, rafterHeight: 18, battenDist: 40,
      },
    },
    hinweisStatik: HINWEIS_STATIK,
  },
  {
    id: 'pult-blech',
    name: 'Pultdach Trapezblech',
    kurzbeschreibung: 'Einseitig geneigtes Pultdach mit Trapezblech. Gefälle über die Breite (Engine fix). Eine homogene Belegungsfläche.',
    status: 'verfuegbar',
    anwendbar: true,
    schlagworte: ['pultdach', 'pult', 'trapezblech', 'blech', 'einseitig', 'schrägdach'],
    geometrie: {
      category: 'pitched', engineShape: 'pult', shapeKey: 'pult',
      defaultLength: 10, defaultWidth: 6, defaultHeight: 4, defaultPitch: 15,
      defaultOverhang: 0.4, defaultOverhangGable: 0.3,
      gefaelleRichtung: 'breite', symmetrie: 'asymmetrisch',
    },
    zimmerer: {
      dachstuhltyp: 'Pultdach (durchlaufende Sparren, Trauf- und Firstpfette)',
      flags: FLAGS_PULT,
      querschnittSparrenCm: [8, 18],
      querschnittPfetteCm: [14, 14],
      materialFestigkeit: 'NH C24 (Richtwert)',
      holzfeuchteProzent: '≤ 20 % (KVH ≤ 15 %)',
      sparrenabstandCm: 70,
      abbundhinweis: 'Sparren über volle Breite, hohe Wand trägt First-, niedrige Wand Traufpfette.',
      spannweiteHinweis: 'Pult-Sparren = volle Breite/cos(α) als ein Feld (Richtwert).',
      lastabtragsweg: 'Sparren → Trauf-/Firstpfette → tragende Wände.',
    },
    dachdecker: pitchedDachdecker({
      empfohleneEindeckung: 'trapezblech',
      rdnGrad: 8, mindestneigungGrad: 5, battenDistCm: 40,
      firstausbildung: 'Wandanschluss-/Firstblech am Hochpunkt',
      lueftungHinweis: 'Belüftungsebene Traufe → First (Richtwert)',
    }),
    pv: pitchedPv({
      belegbareSeiten: ['Pultfläche (main)'],
      ausgeschlosseneSeiten: [],
      empfohleneAusrichtung: 'landscape',
      ukTyp: 'Stehfalz-/Trapez-Klemmen (durchdringungsfrei)',
    }),
    apply: {
      build: {
        category: 'pitched', shape: 'pult', length: 10, width: 6, height: 4, pitch: 15,
        overhang: 0.4, overhangGable: 0.3, rafterSpacing: 70, rafterWidth: 8, rafterHeight: 18, battenDist: 40,
      },
    },
    hinweisStatik: HINWEIS_STATIK,
  },
  {
    id: 'pult-ziegel',
    name: 'Pultdach Ziegel',
    kurzbeschreibung: 'Pultdach mit Ziegeleindeckung, Gefälle über die Breite (Engine fix). Eine zusammenhängende Belegungsfläche.',
    status: 'verfuegbar',
    anwendbar: true,
    schlagworte: ['pultdach', 'pult', 'ziegel', 'einseitig', 'schrägdach'],
    geometrie: {
      category: 'pitched', engineShape: 'pult', shapeKey: 'pult',
      defaultLength: 10, defaultWidth: 6, defaultHeight: 4.5, defaultPitch: 25,
      defaultOverhang: 0.5, defaultOverhangGable: 0.3,
      gefaelleRichtung: 'breite', symmetrie: 'asymmetrisch',
    },
    zimmerer: {
      dachstuhltyp: 'Pultdach (durchlaufende Sparren, Trauf- und Firstpfette)',
      flags: FLAGS_PULT,
      querschnittSparrenCm: [8, 18],
      querschnittPfetteCm: [14, 14],
      materialFestigkeit: 'NH C24 (Richtwert)',
      holzfeuchteProzent: '≤ 20 % (KVH ≤ 15 %)',
      sparrenabstandCm: 70,
      abbundhinweis: 'Ziegel-Auflattung auf Konterlattung; Sparren über volle Breite.',
      spannweiteHinweis: 'Eigenlast Ziegel höher als Blech — Querschnitt prüfen (Richtwert).',
      lastabtragsweg: 'Sparren → Trauf-/Firstpfette → tragende Wände.',
    },
    dachdecker: pitchedDachdecker({ empfohleneEindeckung: 'ziegel', rdnGrad: 22, mindestneigungGrad: 16, battenDistCm: 34 }),
    pv: pitchedPv({ belegbareSeiten: ['Pultfläche (main)'], ausgeschlosseneSeiten: [] }),
    apply: {
      build: {
        category: 'pitched', shape: 'pult', length: 10, width: 6, height: 4.5, pitch: 25,
        overhang: 0.5, overhangGable: 0.3, rafterSpacing: 70, rafterWidth: 8, rafterHeight: 18, battenDist: 34,
      },
    },
    hinweisStatik: HINWEIS_STATIK,
  },
  {
    id: 'walm-standard',
    name: 'Walmdach symmetrisch / Vollwalm',
    kurzbeschreibung: 'Vollwalm mit gleicher Haupt-/Walmneigung. Länge muss größer als Breite sein. Trapez-Haupthänge belegbar, Walmdreiecke Restbelegung, Grate sind Sperrzone.',
    status: 'verfuegbar',
    anwendbar: true,
    schlagworte: ['walmdach', 'walm', 'vollwalm', 'ziegel', 'gratsparren', 'schrägdach'],
    geometrie: {
      category: 'pitched', engineShape: 'walm', shapeKey: 'walm',
      defaultLength: 12, defaultWidth: 8, defaultHeight: 5, defaultPitch: 30,
      defaultOverhang: 0.5, defaultOverhangGable: 0.3,
      symmetrie: 'symmetrisch', benoetigtLaengerGleichBreite: true,
    },
    zimmerer: {
      dachstuhltyp: 'Walmdach (Pfettendach mit 4 Gratsparren, Schifter, Stuhl)',
      flags: FLAGS_WALM,
      querschnittSparrenCm: [8, 18],
      querschnittPfetteCm: [16, 16],
      querschnittGratsparrenCm: [12, 20],
      materialFestigkeit: 'NH C24 (Gratsparren ggf. BSH, Richtwert)',
      holzfeuchteProzent: '≤ 20 % (KVH ≤ 15 %)',
      sparrenabstandCm: 70,
      abbundhinweis: 'Gratsparren als 3D-Länge √(dx²+dy²+dz²); Schifter an Grat anschneiden.',
      spannweiteHinweis: 'Firstlänge = L − 2·Rücksprung; bei gleicher Neigung Rücksprung = W/2 (Richtwert).',
      lastabtragsweg: 'Schifter → Gratsparren/Pfetten → Stuhl → Decke.',
    },
    dachdecker: pitchedDachdecker({
      empfohleneEindeckung: 'ziegel', rdnGrad: 22, mindestneigungGrad: 16,
      gratausbildung: 'Gratziegel auf Gratlatte, an First angearbeitet',
      kehlausbildung: 'keine Innenkehle beim Vollwalm',
    }),
    pv: pitchedPv({
      belegbareSeiten: ['Hauptdach Süd (Trapez)', 'Hauptdach Nord (Trapez)'],
      ausgeschlosseneSeiten: ['Walm West (Restbelegung)', 'Walm Ost (Restbelegung)'],
      sperrzoneGratKehleM: 0.5,
    }),
    apply: {
      build: {
        category: 'pitched', shape: 'walm', length: 12, width: 8, height: 5, pitch: 30,
        overhang: 0.5, overhangGable: 0.3, rafterSpacing: 70, rafterWidth: 8, rafterHeight: 18, battenDist: 34,
      },
    },
    hinweisStatik: HINWEIS_STATIK,
  },
  {
    id: 'flach-bitumen',
    name: 'Flachdach Bitumen (Warmdach)',
    kurzbeschreibung: 'Flachdach als Warmdach mit Bitumen-Schweißbahn und Attika. Reihenabstand/GCR der PV-Aufständerung wird nicht engine-berechnet (Richtwert/geplant).',
    status: 'verfuegbar',
    anwendbar: true,
    schlagworte: ['flachdach', 'flach', 'bitumen', 'warmdach', 'attika', 'rect'],
    geometrie: {
      category: 'flat', engineShape: 'rect', shapeKey: 'rect',
      defaultLength: 12, defaultWidth: 8, defaultHeight: 5, defaultPitch: 3,
      defaultOverhang: 0, defaultOverhangGable: 0, symmetrie: 'symmetrisch',
    },
    zimmerer: {
      dachstuhltyp: 'Massivdecke oder Holzbalken-/Brettstapeldecke (Flachdach, Richtwert)',
      flags: FLAGS_FLACH,
      querschnittSparrenCm: [10, 22],
      materialFestigkeit: 'NH C24 / Stahlbeton (je Konstruktion)',
      holzfeuchteProzent: '≤ 18 % (Holzdecke)',
      sparrenabstandCm: 62.5,
      abbundhinweis: 'Tragdecke mit Gefälledämmung; kein Sparrendach.',
      spannweiteHinweis: 'Durchbiegung/Pfützenbildung beachten; Gefälle ≥ 2 % (Richtwert).',
      lastabtragsweg: 'Abdichtung → Dämmung → Tragdecke → Wände.',
    },
    dachdecker: flatDachdecker({ empfohleneEindeckung: 'bitumen', rdnGrad: 2, mindestneigungGrad: 1.5 }),
    pv: flatPv({ belegbareSeiten: ['Flachdach aufgeständert (Süd)', 'Flachdach aufgeständert (Ost-West)'] }),
    apply: {
      build: {
        category: 'flat', shape: 'rect', length: 12, width: 8, height: 5, pitch: 3,
        overhang: 0, overhangGable: 0, rafterSpacing: 70, rafterWidth: 8, rafterHeight: 18, battenDist: 34, attika: 0.3,
      },
    },
    hinweisStatik: HINWEIS_STATIK,
  },
  {
    id: 'flach-gruendach',
    name: 'Flachdach extensiv begrünt',
    kurzbeschreibung: 'Extensiv begrüntes Flachdach mit Attika, PV als aufgeständerte Ballastlösung. Wind-/Eckzone und Ballast nur als Richtwert.',
    status: 'verfuegbar',
    anwendbar: true,
    schlagworte: ['flachdach', 'flach', 'gründach', 'gruendach', 'begrünt', 'extensiv', 'rect'],
    geometrie: {
      category: 'flat', engineShape: 'rect', shapeKey: 'rect',
      defaultLength: 12, defaultWidth: 8, defaultHeight: 5, defaultPitch: 2,
      defaultOverhang: 0, defaultOverhangGable: 0, symmetrie: 'symmetrisch',
    },
    zimmerer: {
      dachstuhltyp: 'Massivdecke (erhöhte Auflast Substrat/Ballast, Richtwert)',
      flags: FLAGS_FLACH,
      querschnittSparrenCm: [12, 24],
      materialFestigkeit: 'Stahlbeton / NH C24 (je Konstruktion)',
      holzfeuchteProzent: '≤ 18 % (Holzdecke)',
      sparrenabstandCm: 62.5,
      abbundhinweis: 'Wurzelfeste Abdichtung; Substrat-/Ballastauflast statisch berücksichtigen.',
      spannweiteHinweis: 'Zusätzliche Dauerlast aus Substrat (wassergesättigt) als Richtwert ansetzen.',
      lastabtragsweg: 'Vegetation/Substrat → Drän-/Schutzschicht → Abdichtung → Tragdecke.',
    },
    dachdecker: flatDachdecker({
      empfohleneEindeckung: 'gruendach', rdnGrad: 2, mindestneigungGrad: 2,
      unterdeckungKlasse: 'Warmdach mit wurzelfester Abdichtung + Dränage (Richtwert)',
    }),
    pv: flatPv({
      belegbareSeiten: ['Gründach aufgeständert (Ost-West)'],
      ukTyp: 'Aufgeständert mit Ballast über Schutzlage (Richtwert)',
    }),
    apply: {
      build: {
        category: 'flat', shape: 'rect', length: 12, width: 8, height: 5, pitch: 2,
        overhang: 0, overhangGable: 0, rafterSpacing: 70, rafterWidth: 8, rafterHeight: 18, battenDist: 34, attika: 0.3,
      },
    },
    hinweisStatik: HINWEIS_STATIK,
  },
];

interface GeplantSpec {
  id: string;
  name: string;
  shapeKey: VorlagenShapeKey;
  category: RoofCategory;
  grund: string;
  kurz: string;
  schlagworte: string[];
  geom: { length: number; width: number; height: number; pitch: number };
  flags?: ZimmererFlags;
  dachstuhltyp?: string;
}

function geplanteVorlage(s: GeplantSpec): DachformVorlage {
  const istFlach = s.category === 'flat';
  return {
    id: s.id,
    name: s.name,
    kurzbeschreibung: s.kurz,
    status: 'geplant',
    anwendbar: false,
    schlagworte: s.schlagworte,
    geplantGrund: s.grund,
    geometrie: {
      category: s.category,
      engineShape: null,
      shapeKey: s.shapeKey,
      defaultLength: s.geom.length,
      defaultWidth: s.geom.width,
      defaultHeight: s.geom.height,
      defaultPitch: s.geom.pitch,
      defaultOverhang: istFlach ? 0 : 0.5,
      defaultOverhangGable: istFlach ? 0 : 0.3,
    },
    zimmerer: {
      dachstuhltyp: s.dachstuhltyp ?? 'Tragwerk in der Engine noch nicht abgebildet (geplant)',
      flags: s.flags ?? FLAGS_GEPLANT,
      querschnittSparrenCm: [8, 20],
      materialFestigkeit: 'NH C24 (Richtwert)',
      holzfeuchteProzent: '≤ 20 %',
      sparrenabstandCm: 70,
      abbundhinweis: 'Geplant — Abbund erst nach sauberer Geometrie-/Tragwerksumsetzung.',
      spannweiteHinweis: 'Geplant — Spannweiten nach Umsetzung statisch zu prüfen.',
      lastabtragsweg: 'Geplant — Lastabtrag formabhängig festzulegen.',
    },
    dachdecker: istFlach
      ? flatDachdecker({ empfohleneEindeckung: 'bitumen' })
      : pitchedDachdecker({ empfohleneEindeckung: 'ziegel' }),
    pv: istFlach
      ? flatPv({ belegbareSeiten: ['nach Umsetzung festzulegen (geplant)'] })
      : pitchedPv({ belegbareSeiten: ['nach Umsetzung festzulegen (geplant)'], ausgeschlosseneSeiten: ['Sperrzonen formabhängig'] }),
    hinweisStatik: HINWEIS_STATIK,
  };
}

const GEPLANT: DachformVorlage[] = [
  geplanteVorlage({
    id: 'l-shape-pitched', name: 'L-Form Schrägdach', shapeKey: 'l-shape', category: 'pitched',
    kurz: 'Zwei rechtwinklig zusammenstoßende Satteldächer (L). Kehle/Kehlsparren/Schiftung an der einspringenden Ecke.',
    grund: 'buildCompoundPitched hat einen Überlappungs-Bug: L erzeugt dieselben Anbauflächen wie T; Kehle/Kehlsparren/Schiftung und PV-Belegung an der einspringenden Ecke sind nicht stimmig — sichtbar, aber nicht anwendbar.',
    schlagworte: ['l-form', 'l-shape', 'kehle', 'kehlsparren', 'anbau', 'schrägdach'],
    geom: { length: 12, width: 8, height: 5, pitch: 35 },
    dachstuhltyp: 'Zusammengesetztes Sattel-Tragwerk mit Kehle (geplant)',
  }),
  geplanteVorlage({
    id: 't-shape-pitched', name: 'T-Form Schrägdach', shapeKey: 't-shape', category: 'pitched',
    kurz: 'T-förmig zusammenstoßende Satteldächer mit zwei Kehlen.',
    grund: 'Gleicher fehlerhafter buildCompoundPitched-Pfad / Kehlausbildung wie bei L — nicht anwendbar.',
    schlagworte: ['t-form', 't-shape', 'kehle', 'anbau', 'schrägdach'],
    geom: { length: 12, width: 8, height: 5, pitch: 35 },
    dachstuhltyp: 'Zusammengesetztes Sattel-Tragwerk mit zwei Kehlen (geplant)',
  }),
  // l-shape-flat ist jetzt VERFÜGBAR (echtes Flachdach-L-Polygon) -> siehe VERFUEGBAR_GRUNDRISS.
  geplanteVorlage({
    id: 'zeltdach', name: 'Zeltdach', shapeKey: 'zeltdach', category: 'pitched',
    kurz: 'Vier gleich geneigte Dreieckflächen, die in einer Spitze zusammenlaufen.',
    grund: 'Kein Build-Pfad in updateBuilding. Nur quadratisch (L=B) mit gleicher Neigung wäre symmetrisch — sonst entstünde still falsche Geometrie. Erst nach Konsistenzprüfung baubar.',
    schlagworte: ['zeltdach', 'pyramide', 'spitze', 'grat', 'schrägdach'],
    geom: { length: 8, width: 8, height: 5, pitch: 30 },
    flags: { ...FLAGS_WALM, firstpfette: false }, dachstuhltyp: 'Zeltdach (4 Gratsparren, ohne First — geplant)',
  }),
  geplanteVorlage({
    id: 'krueppelwalm', name: 'Krüppelwalmdach', shapeKey: 'krueppelwalm', category: 'pitched',
    kurz: 'Satteldach mit verkürztem Walm (Krüppelwalm) am Giebel.',
    grund: 'Nur roofModel.ts baut ihn, der Vorlage-Apply-Pfad updateBuilding NICHT (nur sattel/pult/walm/rect). Braucht abweichende Walmneigung — als verfügbar würde ein leeres/falsches Dach entstehen → geplant.',
    schlagworte: ['krüppelwalm', 'krueppelwalm', 'walm', 'giebel', 'schrägdach'],
    geom: { length: 12, width: 8, height: 5, pitch: 38 },
    flags: FLAGS_WALM, dachstuhltyp: 'Krüppelwalm (Teilwalm mit Giebeldreieck — geplant)',
  }),
  geplanteVorlage({
    id: 'mansard', name: 'Mansarddach', shapeKey: 'mansard', category: 'pitched',
    kurz: 'Zwei Neigungsabschnitte je Dachfläche (steiler unten, flacher oben) mit Knickpfette.',
    grund: 'Zwei Neigungsabschnitte je Fläche + Knickpfette sind in der Engine nicht umgesetzt.',
    schlagworte: ['mansarddach', 'mansard', 'knick', 'gauben', 'schrägdach'],
    geom: { length: 12, width: 9, height: 5, pitch: 60 },
    dachstuhltyp: 'Mansarde (unterer Steilteil + oberer Flachteil, Knickpfette — geplant)',
  }),
  geplanteVorlage({
    id: 'mansardwalm', name: 'Mansardwalmdach', shapeKey: 'mansardwalm', category: 'pitched',
    kurz: 'Mansarddach kombiniert mit Walm — doppelter Neigungsknick auch an den Stirnseiten.',
    grund: 'Mansard + Walm mit doppeltem Neigungsknick ist nicht umgesetzt.',
    schlagworte: ['mansardwalm', 'mansard', 'walm', 'knick', 'schrägdach'],
    geom: { length: 13, width: 9, height: 5, pitch: 60 },
    flags: FLAGS_WALM, dachstuhltyp: 'Mansardwalm (Knick + Gratsparren — geplant)',
  }),
  geplanteVorlage({
    id: 'schleppdach', name: 'Schleppdach (Hauptdachform)', shapeKey: 'schleppdach', category: 'pitched',
    kurz: 'Angeschlepptes einseitiges Gefälle als eigenständige Hauptdachform.',
    grund: 'Kein Build-Pfad — die Schleppe existiert nur als Aufbau (Schleppgaube), nicht als Hauptdach.',
    schlagworte: ['schleppdach', 'schleppe', 'angeschleppt', 'pult', 'schrägdach'],
    geom: { length: 10, width: 5, height: 4, pitch: 20 },
    flags: FLAGS_PULT, dachstuhltyp: 'Schleppe (einseitiges Gefälle — geplant)',
  }),
  geplanteVorlage({
    id: 'versetztes-pult', name: 'Versetztes Pultdach', shapeKey: 'versetztes-pult', category: 'pitched',
    kurz: 'Zwei versetzte Pultflächen mit Hochzug/Fensterband dazwischen.',
    grund: 'Zwei versetzte Pultflächen mit Hochzug/Fensterband haben keinen Build-Pfad.',
    schlagworte: ['versetztes pult', 'versetztes-pult', 'pult', 'fensterband', 'schrägdach'],
    geom: { length: 12, width: 8, height: 4, pitch: 15 },
    flags: FLAGS_PULT, dachstuhltyp: 'Zwei versetzte Pultebenen (geplant)',
  }),
  geplanteVorlage({
    id: 'schmetterling', name: 'Schmetterlingsdach', shapeKey: 'schmetterling', category: 'pitched',
    kurz: 'V-förmige Innenkehle mit Mittelentwässerung (zwei nach innen geneigte Flächen).',
    grund: 'V-förmige Innenkehle mit Mittelentwässerung ist nicht umgesetzt.',
    schlagworte: ['schmetterlingsdach', 'schmetterling', 'kehle', 'v-form', 'schrägdach'],
    geom: { length: 11, width: 8, height: 4, pitch: 12 },
    dachstuhltyp: 'V-Innenkehle mit Mittelrinne (geplant)',
  }),
  geplanteVorlage({
    id: 'grabendach', name: 'Grabendach', shapeKey: 'grabendach', category: 'pitched',
    kurz: 'Aneinandergereihte Satteldächer mit Innenrinnen (Grabenentwässerung).',
    grund: 'Aneinandergereihte Sättel mit Innenrinnen sind nicht umgesetzt.',
    schlagworte: ['grabendach', 'reihung', 'innenrinne', 'sattel', 'schrägdach'],
    geom: { length: 20, width: 16, height: 5, pitch: 25 },
    dachstuhltyp: 'Mehrfach-Sattel mit Kehlrinnen (geplant)',
  }),
  geplanteVorlage({
    id: 'sheddach', name: 'Sheddach (Sägezahn)', shapeKey: 'sheddach', category: 'pitched',
    kurz: 'Reihung asymmetrischer Pulte mit Nordlicht (Sägezahnprofil).',
    grund: 'Reihung asymmetrischer Pulte mit Nordlicht ist nicht umgesetzt.',
    schlagworte: ['sheddach', 'sägezahn', 'saegezahn', 'nordlicht', 'halle', 'schrägdach'],
    geom: { length: 24, width: 18, height: 6, pitch: 30 },
    dachstuhltyp: 'Sägezahn-Binderreihe (geplant)',
  }),
  geplanteVorlage({
    id: 'tonnendach', name: 'Tonnendach', shapeKey: 'tonnendach', category: 'pitched',
    kurz: 'Gekrümmte Tonnenschale auf Bogenbindern (BSH).',
    grund: 'Gekrümmte Tragstruktur (Bogenbinder/BSH) ohne ebene Sparrenflächen — nicht umgesetzt.',
    schlagworte: ['tonnendach', 'tonne', 'bogen', 'bsh', 'gekrümmt'],
    geom: { length: 18, width: 12, height: 5, pitch: 0 },
    flags: FLAGS_BOGEN, dachstuhltyp: 'Bogenbinder/Tonnenschale (geplant)',
  }),
  geplanteVorlage({
    id: 'bogendach', name: 'Bogendach', shapeKey: 'bogendach', category: 'pitched',
    kurz: 'Einfach gekrümmte Dachfläche entlang eines Bogens.',
    grund: 'Gekrümmte Fläche (Lathe/Extrude entlang Bogen) ist nicht umgesetzt.',
    schlagworte: ['bogendach', 'bogen', 'gekrümmt', 'rund'],
    geom: { length: 16, width: 10, height: 5, pitch: 0 },
    flags: FLAGS_BOGEN, dachstuhltyp: 'Bogenträger (geplant)',
  }),
  geplanteVorlage({
    id: 'u-grundriss', name: 'U-Grundriss', shapeKey: 'u-grundriss', category: 'pitched',
    kurz: 'U-förmiger Baukörper mit mehreren Kehlen und Graten.',
    grund: 'Mehrfache Kehlen/Grate, erbt den Compound-Bug — nicht umgesetzt.',
    schlagworte: ['u-grundriss', 'u-form', 'kehle', 'grat', 'mehrflügel', 'schrägdach'],
    geom: { length: 16, width: 14, height: 5, pitch: 35 },
    flags: FLAGS_WALM, dachstuhltyp: 'U-Baukörper mit Mehrfach-Kehlen (geplant)',
  }),
  geplanteVorlage({
    id: 'mehrfluegel', name: 'Mehrflügelbau', shapeKey: 'mehrfluegel', category: 'pitched',
    kurz: 'Zusammengesetzte Geometrie mit mehreren Flügeln, Kehlen und Graten.',
    grund: 'Zusammengesetzte Geometrie mit mehreren Kehlen ist nicht umgesetzt.',
    schlagworte: ['mehrflügel', 'mehrfluegel', 'flügel', 'kehle', 'grat', 'schrägdach'],
    geom: { length: 20, width: 16, height: 5, pitch: 35 },
    flags: FLAGS_WALM, dachstuhltyp: 'Mehrflügel mit Kehlen/Graten (geplant)',
  }),
  geplanteVorlage({
    id: 'halle', name: 'Hallen-Sonderform', shapeKey: 'halle', category: 'pitched',
    kurz: 'Binder-Hallentragwerk (Stahl/BSH) mit großen Spannweiten.',
    grund: 'Binder-Hallentragwerk mit großen Spannweiten ist als Sonderform nicht umgesetzt.',
    schlagworte: ['halle', 'binder', 'gewerbe', 'spannweite', 'sonderform'],
    geom: { length: 40, width: 20, height: 6, pitch: 10 },
    dachstuhltyp: 'Hallenbinder (Stahl/BSH — geplant)',
  }),
];

// =====================================================================
// 7b. ERWEITERTE BIBLIOTHEK (additiv) — verfügbare Vorlagen
// =====================================================================
// Engine baut diese Grundformen zuverlässig (sattel/pult/walm/rect). Aufbauten-
// und Gebäudetyp-Varianten sind verfügbar; Aufbauten (Gaube/Kamin/Dachfenster …)
// werden als Obstacles separat ergänzt — apply setzt das Basisdach via setBuild/
// setCover. Bestehende 25 Vorlagen bleiben unverändert.
const VERFUEGBAR_NEU: DachformVorlage[] = [
  // — EINFACH: Satteldach-Varianten —
  verfSattel({ id: 'sattel-grosser-ueberstand', name: 'Satteldach mit großem Überstand', kurz: 'Symmetrisches Satteldach mit großzügigem Trauf- und Ortgangüberstand (Verschattung/Witterungsschutz).', schlagworte: ['satteldach', 'sattel', 'überstand', 'ueberstand', 'efh', 'schrägdach'], length: 10, width: 8, height: 5, pitch: 35, overhang: 0.9, overhangGable: 0.6 }),
  verfSattel({ id: 'sattel-ohne-ueberstand', name: 'Satteldach ohne Überstand', kurz: 'Satteldach mit bündigem Abschluss ohne Dachüberstand (städtisch/Reihenbebauung).', schlagworte: ['satteldach', 'sattel', 'ohne überstand', 'bündig', 'schrägdach'], length: 10, width: 8, height: 5, pitch: 35, overhang: 0, overhangGable: 0 }),
  verfSattel({ id: 'sattel-ost-west', name: 'Satteldach Ost-West', kurz: 'Satteldach mit Firstrichtung Nord-Süd → beide Hauptflächen nach Ost und West (gleichmäßige PV-Verteilung).', schlagworte: ['satteldach', 'sattel', 'ost-west', 'ausrichtung', 'pv-tauglich', 'schrägdach'], length: 10, width: 8, height: 5, pitch: 30, pv: { belegbareSeiten: ['Hauptdach Ost', 'Hauptdach West'], ausgeschlosseneSeiten: [] } }),
  verfSattel({ id: 'sattel-nord-sued', name: 'Satteldach Nord-Süd', kurz: 'Satteldach mit Firstrichtung Ost-West → Süd- und Nordhang; Südhang voll belegbar, Nordhang ausgeschlossen.', schlagworte: ['satteldach', 'sattel', 'nord-süd', 'ausrichtung', 'schrägdach'], length: 10, width: 8, height: 5, pitch: 35, pv: { belegbareSeiten: ['Hauptdach Süd'], ausgeschlosseneSeiten: ['Nordhang'] } }),

  // — EINFACH: Pultdach-Varianten —
  verfPult({ id: 'pult-standard', name: 'Pultdach Standard', kurz: 'Einseitig geneigtes Pultdach (Standardneigung), Gefälle über die Breite (Engine fix).', schlagworte: ['pultdach', 'pult', 'standard', 'einseitig', 'schrägdach'], length: 10, width: 6, height: 4.5, pitch: 20 }),
  verfPult({ id: 'pult-flach', name: 'Pultdach flach', kurz: 'Flach geneigtes Pultdach mit Trapezblech; geringe Neigung → regensichere Ausführung beachten.', schlagworte: ['pultdach', 'pult', 'flach', 'trapezblech', 'schrägdach'], length: 10, width: 6, height: 4, pitch: 8, cover: 'trapezblech', battenDist: 40, dd: { rdnGrad: 8, mindestneigungGrad: 5 } }),
  verfPult({ id: 'pult-steil', name: 'Pultdach steil', kurz: 'Steil geneigtes Pultdach (markante Schrägkante, gute PV-Anstellung).', schlagworte: ['pultdach', 'pult', 'steil', 'schrägdach'], length: 10, width: 6, height: 5, pitch: 35 }),
  verfPult({ id: 'pult-sued', name: 'Pultdach Süd', kurz: 'Pultfläche nach Süden orientiert → maximaler PV-Ertrag auf einer homogenen Fläche.', schlagworte: ['pultdach', 'pult', 'süd', 'pv-tauglich', 'schrägdach'], length: 10, width: 6, height: 4.5, pitch: 20, pv: { belegbareSeiten: ['Pultfläche Süd'], ausgeschlosseneSeiten: [] } }),
  verfPult({ id: 'pult-ost', name: 'Pultdach Ost', kurz: 'Pultfläche nach Osten orientiert (Morgenertrag).', schlagworte: ['pultdach', 'pult', 'ost', 'schrägdach'], length: 10, width: 6, height: 4.5, pitch: 20, pv: { belegbareSeiten: ['Pultfläche Ost'], ausgeschlosseneSeiten: [] } }),
  verfPult({ id: 'pult-west', name: 'Pultdach West', kurz: 'Pultfläche nach Westen orientiert (Abendertrag).', schlagworte: ['pultdach', 'pult', 'west', 'schrägdach'], length: 10, width: 6, height: 4.5, pitch: 20, pv: { belegbareSeiten: ['Pultfläche West'], ausgeschlosseneSeiten: [] } }),

  // — EINFACH: Flachdach-Varianten —
  verfFlach({ id: 'flach-standard', name: 'Flachdach Standard', kurz: 'Klassisches Flachdach (Warmdach) mit minimalem Gefälle und umlaufender Attika.', schlagworte: ['flachdach', 'flach', 'warmdach', 'attika', 'rect'], length: 12, width: 8, height: 5, pitch: 3, cover: 'bitumen' }),
  verfFlach({ id: 'flach-attika', name: 'Flachdach mit Attika', kurz: 'Flachdach mit erhöhter Attika (verdeckte Entwässerung, Absturzsicherung).', schlagworte: ['flachdach', 'flach', 'attika', 'rect'], length: 12, width: 8, height: 5, pitch: 3, cover: 'kunststoff', attika: 0.6 }),
  verfFlach({ id: 'flach-garage', name: 'Flachdach Garage', kurz: 'Kleines Garagen-Flachdach mit Kunststoffabdichtung; kompakte Belegungsfläche.', schlagworte: ['flachdach', 'flach', 'garage', 'nebengebäude', 'rect'], length: 6, width: 3, height: 2.6, pitch: 2, cover: 'kunststoff', attika: 0.2 }),
  verfFlach({ id: 'flach-gewerbe', name: 'Flachdach Gewerbe', kurz: 'Gewerbliches Flachdach mit großer zusammenhängender Dachfläche.', schlagworte: ['flachdach', 'flach', 'gewerbe', 'rect'], length: 24, width: 16, height: 6, pitch: 2.5, cover: 'kunststoff', attika: 0.4 }),
  verfFlach({ id: 'flach-halle', name: 'Flachdach Halle', kurz: 'Großflächiges Hallen-Flachdach; PV als aufgeständerte Ost-West-Belegung.', schlagworte: ['flachdach', 'flach', 'halle', 'industrie', 'rect'], length: 40, width: 20, height: 7, pitch: 2, cover: 'kunststoff', attika: 0.4, pv: { belegbareSeiten: ['Hallendach aufgeständert (Ost-West)'] } }),

  // — Walmdach-Varianten (Länge > Breite Pflicht) —
  verfWalm({ id: 'walm-flach', name: 'Walmdach flach', kurz: 'Flach geneigtes Walmdach (niedrige Trauf-/Firsthöhe). Länge > Breite Pflicht.', schlagworte: ['walmdach', 'walm', 'flach', 'gratsparren', 'schrägdach'], length: 13, width: 9, height: 4.5, pitch: 22 }),
  verfWalm({ id: 'walm-steil', name: 'Walmdach steil', kurz: 'Steiles Walmdach mit ausgeprägten Walmflächen und langen Gratsparren.', schlagworte: ['walmdach', 'walm', 'steil', 'gratsparren', 'schrägdach'], length: 13, width: 9, height: 5.5, pitch: 40 }),
  verfWalm({ id: 'walm-stadtvilla', name: 'Stadtvilla Walmdach', kurz: 'Repräsentatives Walmdach einer Stadtvilla (symmetrisch, moderate Neigung).', schlagworte: ['walmdach', 'walm', 'stadtvilla', 'gebäudetyp', 'schrägdach'], length: 13, width: 11, height: 6, pitch: 28 }),
  verfWalm({ id: 'walm-bungalow', name: 'Bungalow Walmdach', kurz: 'Flaches, breit gelagertes Walmdach eines Bungalows (eingeschossig).', schlagworte: ['walmdach', 'walm', 'bungalow', 'gebäudetyp', 'schrägdach'], length: 15, width: 11, height: 3.2, pitch: 22 }),

  // — GAUBEN (Basisdach + Aufbauten als Sperrflächen/Obstacles) —
  verfSattel({ id: 'sattel-schleppgaube-1', name: 'Satteldach mit einer Schleppgaube', kurz: 'Satteldach mit einer Schleppgaube als Aufbau (Gaube wird als Sperrfläche/Obstacle ergänzt).', schlagworte: ['satteldach', 'sattel', 'gaube', 'schleppgaube', 'schlepp', 'aufbau', 'schrägdach'], length: 10, width: 8, height: 5, pitch: 40, pv: { belegbareSeiten: ['Hauptdach Süd (um Gaube ausgespart)'], ausgeschlosseneSeiten: ['Nordhang', 'Gaubenzone'] } }),
  verfSattel({ id: 'sattel-schleppgaube-2', name: 'Satteldach mit zwei Schleppgauben', kurz: 'Satteldach mit zwei Schleppgauben (Sperrflächen um beide Gauben).', schlagworte: ['satteldach', 'sattel', 'gaube', 'schleppgaube', 'zwei', 'schlepp', 'aufbau', 'schrägdach'], length: 12, width: 8, height: 5, pitch: 40, pv: { belegbareSeiten: ['Hauptdach Süd (um Gauben ausgespart)'], ausgeschlosseneSeiten: ['Nordhang', '2x Gaubenzone'] } }),
  verfSattel({ id: 'sattel-giebelgaube', name: 'Satteldach mit Satteldachgaube (Giebelgaube)', kurz: 'Satteldach mit giebelständiger Satteldachgaube als Aufbau.', schlagworte: ['satteldach', 'sattel', 'gaube', 'giebelgaube', 'satteldachgaube', 'aufbau', 'schrägdach'], length: 10, width: 8, height: 5, pitch: 42 }),
  verfSattel({ id: 'sattel-walmgaube', name: 'Satteldach mit Walmdachgaube', kurz: 'Satteldach mit Walmdachgaube (abgewalmte Gaube) als Aufbau.', schlagworte: ['satteldach', 'sattel', 'gaube', 'walmgaube', 'walmdachgaube', 'aufbau', 'schrägdach'], length: 10, width: 8, height: 5, pitch: 40 }),
  verfSattel({ id: 'sattel-flachgaube', name: 'Satteldach mit Flachdachgaube', kurz: 'Satteldach mit moderner Flachdachgaube (flacher Gaubendeckel) als Aufbau.', schlagworte: ['satteldach', 'sattel', 'gaube', 'flachgaube', 'flachdachgaube', 'aufbau', 'schrägdach'], length: 10, width: 8, height: 5, pitch: 38 }),
  verfWalm({ id: 'walm-gaube', name: 'Walmdach mit Gaube', kurz: 'Walmdach mit Gaube als Aufbau auf der Hauptfläche (Länge > Breite).', schlagworte: ['walmdach', 'walm', 'gaube', 'aufbau', 'schrägdach'], length: 13, width: 9, height: 5, pitch: 35 }),
  verfPult({ id: 'pult-gaube', name: 'Pultdach mit Gaube', kurz: 'Pultdach mit Gaube/Belichtungsaufbau als Sperrfläche.', schlagworte: ['pultdach', 'pult', 'gaube', 'aufbau', 'schrägdach'], length: 10, width: 6, height: 4.5, pitch: 25 }),
  verfSattel({ id: 'dach-breite-gaube', name: 'Dach mit breiter Gaube', kurz: 'Satteldach mit einer durchgehend breiten Gaube (große Sperrfläche im Hauptdach).', schlagworte: ['satteldach', 'sattel', 'gaube', 'breit', 'breite gaube', 'aufbau', 'schrägdach'], length: 12, width: 8, height: 5, pitch: 40 }),
  verfSattel({ id: 'dach-mehrere-kleine-gauben', name: 'Dach mit mehreren kleinen Gauben', kurz: 'Satteldach mit mehreren kleinen Gauben (mehrere kleine Sperrflächen).', schlagworte: ['satteldach', 'sattel', 'gaube', 'mehrere', 'kleine', 'aufbau', 'schrägdach'], length: 14, width: 8, height: 5, pitch: 42 }),
  verfSattel({ id: 'gaube-dachfenster', name: 'Gaube + Dachfenster', kurz: 'Satteldach mit Gaube und zusätzlichen Dachfenstern (kombinierte Sperrflächen).', schlagworte: ['satteldach', 'sattel', 'gaube', 'dachfenster', 'aufbau', 'schrägdach'], length: 11, width: 8, height: 5, pitch: 40 }),
  verfSattel({ id: 'gaube-kamin', name: 'Gaube + Kamin', kurz: 'Satteldach mit Gaube und Kamin (Sperrflächen Gaube und Schornstein).', schlagworte: ['satteldach', 'sattel', 'gaube', 'kamin', 'schornstein', 'aufbau', 'schrägdach'], length: 11, width: 8, height: 5, pitch: 40 }),

  // — AUFBAUTEN (Basisdach verfügbar; Aufbauten als Obstacles) —
  verfSattel({ id: 'sattel-2-dachfenster', name: 'Satteldach mit 2 Dachfenstern', kurz: 'Satteldach mit zwei Dachflächenfenstern (Sperrflächen in der Belegung).', schlagworte: ['satteldach', 'sattel', 'dachfenster', 'aufbau', 'schrägdach'], length: 10, width: 8, height: 5, pitch: 40 }),
  verfSattel({ id: 'sattel-4-dachfenster', name: 'Satteldach mit 4 Dachfenstern', kurz: 'Satteldach mit vier Dachflächenfenstern (mehrere Sperrflächen).', schlagworte: ['satteldach', 'sattel', 'dachfenster', 'fenster4', 'vier', 'aufbau', 'schrägdach'], length: 12, width: 8, height: 5, pitch: 40 }),
  verfSattel({ id: 'sattel-kamin', name: 'Satteldach mit Kamin', kurz: 'Satteldach mit Schornstein/Kamin nahe First (Sperrfläche + Verschattung).', schlagworte: ['satteldach', 'sattel', 'kamin', 'schornstein', 'aufbau', 'schrägdach'], length: 10, width: 8, height: 5, pitch: 35 }),
  verfSattel({ id: 'sattel-kamin-dachfenster', name: 'Satteldach mit Kamin + Dachfenster', kurz: 'Satteldach mit Kamin und Dachfenstern (kombinierte Sperrflächen).', schlagworte: ['satteldach', 'sattel', 'kamin', 'dachfenster', 'aufbau', 'schrägdach'], length: 11, width: 8, height: 5, pitch: 38 }),
  verfWalm({ id: 'walm-dachfenster', name: 'Walmdach mit Dachfenstern', kurz: 'Walmdach mit Dachfenstern auf den Hauptflächen (Länge > Breite).', schlagworte: ['walmdach', 'walm', 'dachfenster', 'aufbau', 'schrägdach'], length: 13, width: 9, height: 5, pitch: 32 }),
  verfWalm({ id: 'walm-kamin', name: 'Walmdach mit Kamin', kurz: 'Walmdach mit Schornstein als Sperrfläche (Länge > Breite).', schlagworte: ['walmdach', 'walm', 'kamin', 'schornstein', 'aufbau', 'schrägdach'], length: 13, width: 9, height: 5, pitch: 30 }),
  verfPult({ id: 'pult-dachfenster', name: 'Pultdach mit Dachfenstern', kurz: 'Pultdach mit Dachfenstern in der geneigten Fläche.', schlagworte: ['pultdach', 'pult', 'dachfenster', 'aufbau', 'schrägdach'], length: 10, width: 6, height: 4.5, pitch: 22 }),
  verfFlach({ id: 'flach-lichtkuppel', name: 'Flachdach mit Lichtkuppel', kurz: 'Flachdach mit Lichtkuppel (Oberlicht) als Sperrfläche.', schlagworte: ['flachdach', 'flach', 'lichtkuppel', 'oberlicht', 'aufbau', 'rect'], length: 12, width: 8, height: 5, pitch: 3, cover: 'kunststoff' }),
  verfFlach({ id: 'flach-luefter', name: 'Flachdach mit Lüftern', kurz: 'Flachdach mit mehreren Entlüftern/Dunstrohren (Sperrflächen).', schlagworte: ['flachdach', 'flach', 'lüfter', 'luefter', 'entlüftung', 'aufbau', 'rect'], length: 12, width: 8, height: 5, pitch: 3, cover: 'bitumen' }),
  verfFlach({ id: 'flach-rauchabzug', name: 'Flachdach mit Rauchabzug', kurz: 'Flachdach mit Rauch-/Wärmeabzug (RWA) als Sperrfläche.', schlagworte: ['flachdach', 'flach', 'rauchabzug', 'rwa', 'lüfter', 'aufbau', 'rect'], length: 14, width: 10, height: 6, pitch: 2.5, cover: 'kunststoff' }),
  verfSattel({ id: 'dach-mehrere-sperrflaechen', name: 'Dach mit mehreren Sperrflächen', kurz: 'Satteldach mit mehreren Sperrflächen (Gaube, Kamin, Dachfenster) zur Belegungsplanung.', schlagworte: ['satteldach', 'sattel', 'sperrfläche', 'gaube', 'kamin', 'dachfenster', 'aufbau', 'schrägdach'], length: 12, width: 8, height: 5, pitch: 40 }),
  verfSattel({ id: 'dach-kamin-luefter', name: 'Dach mit Kamin + Lüfter', kurz: 'Satteldach mit Kamin und Entlüftern (kombinierte Sperrflächen).', schlagworte: ['satteldach', 'sattel', 'kamin', 'lüfter', 'luefter', 'aufbau', 'schrägdach'], length: 11, width: 8, height: 5, pitch: 35 }),
  verfSattel({ id: 'dach-sat-anlage', name: 'Dach mit Sat-Anlage', kurz: 'Satteldach mit Sat-Schüssel (Sperrfläche + Verschattung).', schlagworte: ['satteldach', 'sattel', 'sat', 'satellit', 'aufbau', 'schrägdach'], length: 10, width: 8, height: 5, pitch: 35 }),
  verfSattel({ id: 'dach-schneefang', name: 'Dach mit Schneefang', kurz: 'Satteldach mit traufseitigem Schneefang (Schneelastzone beachten).', schlagworte: ['satteldach', 'sattel', 'schneefang', 'aufbau', 'schrägdach'], length: 10, width: 8, height: 5, pitch: 35 }),

  // — PV-OPTIMIERT (verfügbar) —
  verfSattel({ id: 'pv-sattel-sued', name: 'Satteldach Süd (PV)', kurz: 'PV-optimiertes Satteldach mit voll belegtem Südhang, Nordhang ausgeschlossen.', schlagworte: ['satteldach', 'sattel', 'pv', 'süd', 'photovoltaik', 'schrägdach'], length: 11, width: 8, height: 5, pitch: 30, pv: { belegbareSeiten: ['Hauptdach Süd (Vollbelegung)'], ausgeschlosseneSeiten: ['Nordhang'] } }),
  verfSattel({ id: 'pv-sattel-ost-west', name: 'Ost-West-Satteldach (PV)', kurz: 'PV-optimiertes Satteldach mit Ost-West-Belegung beider Hauptflächen.', schlagworte: ['satteldach', 'sattel', 'pv', 'ost-west', 'photovoltaik', 'schrägdach'], length: 11, width: 8, height: 5, pitch: 25, pv: { belegbareSeiten: ['Hauptdach Ost', 'Hauptdach West'], ausgeschlosseneSeiten: [] } }),
  verfPult({ id: 'pv-pult-sued', name: 'Pultdach Süd (PV)', kurz: 'PV-optimiertes Pultdach mit nach Süden geneigter Vollfläche.', schlagworte: ['pultdach', 'pult', 'pv', 'süd', 'photovoltaik', 'schrägdach'], length: 11, width: 7, height: 4.5, pitch: 18, pv: { belegbareSeiten: ['Pultfläche Süd (Vollbelegung)'], ausgeschlosseneSeiten: [] } }),
  verfFlach({ id: 'pv-flach-sued-aufstaenderung', name: 'Flachdach Süd-Aufständerung (PV)', kurz: 'Flachdach mit nach Süden aufgeständerter PV (Reihenabstand/GCR als Richtwert, nicht engine-berechnet).', schlagworte: ['flachdach', 'flach', 'pv', 'süd', 'aufständerung', 'photovoltaik', 'rect'], length: 14, width: 10, height: 6, pitch: 3, cover: 'kunststoff', pv: { belegbareSeiten: ['Flachdach aufgeständert (Süd)'], empfohleneAusrichtung: 'portrait' } }),
  verfFlach({ id: 'pv-flach-ost-west-aufstaenderung', name: 'Flachdach Ost-West-Aufständerung (PV)', kurz: 'Flachdach mit Ost-West-Aufständerung (höhere Flächenausnutzung, geringerer Reihenabstand).', schlagworte: ['flachdach', 'flach', 'pv', 'ost-west', 'aufständerung', 'photovoltaik', 'rect'], length: 14, width: 10, height: 6, pitch: 3, cover: 'kunststoff', pv: { belegbareSeiten: ['Flachdach aufgeständert (Ost-West)'] } }),
  verfFlach({ id: 'pv-garage', name: 'Garage (PV)', kurz: 'Garagen-Flachdach mit PV-Belegung (kleine, aufgeständerte Fläche).', schlagworte: ['flachdach', 'flach', 'garage', 'pv', 'photovoltaik', 'rect'], length: 6, width: 3, height: 2.6, pitch: 2, cover: 'kunststoff', attika: 0.2 }),
  verfPult({ id: 'pv-carport', name: 'Carport Pultdach (PV)', kurz: 'Carport als Pultdach mit nach Süden geneigter PV-Fläche (Solar-Carport).', schlagworte: ['carport', 'pult', 'pv', 'photovoltaik', 'solar-carport', 'schrägdach'], length: 6, width: 5, height: 2.6, pitch: 10, cover: 'trapezblech', battenDist: 40, dd: { rdnGrad: 8, mindestneigungGrad: 5 }, pv: { belegbareSeiten: ['Carport-Pultfläche'] } }),
  verfFlach({ id: 'pv-gewerbehalle', name: 'Gewerbehalle Flachdach (PV)', kurz: 'Gewerbehallen-Flachdach mit großflächiger aufgeständerter PV (Ost-West).', schlagworte: ['flachdach', 'flach', 'gewerbe', 'halle', 'pv', 'photovoltaik', 'rect'], length: 32, width: 18, height: 7, pitch: 2.5, cover: 'kunststoff', attika: 0.4, pv: { belegbareSeiten: ['Hallendach aufgeständert (Ost-West)'] } }),
  verfSattel({ id: 'pv-scheune', name: 'Scheune Satteldach (PV)', kurz: 'Scheunen-Satteldach mit großer Süd-Dachfläche für PV.', schlagworte: ['satteldach', 'sattel', 'scheune', 'pv', 'photovoltaik', 'landwirtschaft', 'schrägdach'], length: 20, width: 12, height: 5, pitch: 22, cover: 'trapezblech', battenDist: 40, dd: { rdnGrad: 18, mindestneigungGrad: 5 }, pv: { belegbareSeiten: ['Scheunendach Süd (Vollbelegung)'], ausgeschlosseneSeiten: ['Nordhang'] } }),
  verfSattel({ id: 'pv-nur-suedseite', name: 'Nur Südseite belegt (PV)', kurz: 'Satteldach, PV ausschließlich auf der Südseite (Nordhang frei).', schlagworte: ['satteldach', 'sattel', 'pv', 'süd', 'nur südseite', 'photovoltaik', 'schrägdach'], length: 10, width: 8, height: 5, pitch: 32, pv: { belegbareSeiten: ['Hauptdach Süd'], ausgeschlosseneSeiten: ['Nordhang'] } }),
  verfSattel({ id: 'pv-ost-west-belegung', name: 'Ost-West-Belegung (PV)', kurz: 'Satteldach mit PV auf Ost- und Westfläche (verteilte Tageskurve).', schlagworte: ['satteldach', 'sattel', 'pv', 'ost-west', 'photovoltaik', 'schrägdach'], length: 11, width: 8, height: 5, pitch: 25, pv: { belegbareSeiten: ['Hauptdach Ost', 'Hauptdach West'], ausgeschlosseneSeiten: [] } }),
  verfSattel({ id: 'pv-nordseite-ausgeschlossen', name: 'Nordseite ausgeschlossen (PV)', kurz: 'Satteldach mit explizit ausgeschlossener Nordseite (nur Süd belegt).', schlagworte: ['satteldach', 'sattel', 'pv', 'nord', 'ausgeschlossen', 'photovoltaik', 'schrägdach'], length: 10, width: 8, height: 5, pitch: 35, pv: { belegbareSeiten: ['Hauptdach Süd'], ausgeschlosseneSeiten: ['Nordhang (ausgeschlossen)'] } }),
  verfSattel({ id: 'pv-sperrflaechen-kamin-gauben', name: 'Sperrflächen Kamin + Gauben (PV)', kurz: 'Satteldach mit PV-Belegung um Kamin und Gauben (Sperrflächen ausgespart).', schlagworte: ['satteldach', 'sattel', 'pv', 'sperrfläche', 'kamin', 'gaube', 'mehrere', 'photovoltaik', 'schrägdach'], length: 12, width: 8, height: 5, pitch: 38, pv: { belegbareSeiten: ['Hauptdach Süd (um Sperrflächen)'], ausgeschlosseneSeiten: ['Nordhang', 'Kaminzone', 'Gaubenzone'] } }),

  // — GEBÄUDETYP (verfügbar, ein Baukörper) —
  verfSattel({ id: 'efh-sattel', name: 'EFH Satteldach', kurz: 'Einfamilienhaus mit klassischem Satteldach (Standardproportion).', schlagworte: ['efh', 'einfamilienhaus', 'satteldach', 'sattel', 'gebäudetyp', 'schrägdach'], length: 10, width: 8, height: 5, pitch: 38 }),
  verfWalm({ id: 'efh-walm', name: 'EFH Walmdach', kurz: 'Einfamilienhaus mit Walmdach (allseitig abgewalmt, Länge > Breite).', schlagworte: ['efh', 'einfamilienhaus', 'walmdach', 'walm', 'gebäudetyp', 'schrägdach'], length: 12, width: 9, height: 5, pitch: 30 }),
  verfPult({ id: 'efh-pult', name: 'EFH Pultdach', kurz: 'Modernes Einfamilienhaus mit Pultdach (einseitiges Gefälle).', schlagworte: ['efh', 'einfamilienhaus', 'pultdach', 'pult', 'gebäudetyp', 'schrägdach'], length: 10, width: 7, height: 5, pitch: 18 }),
  verfFlach({ id: 'efh-flach', name: 'EFH Flachdach', kurz: 'Einfamilienhaus im Bauhausstil mit Flachdach und Attika.', schlagworte: ['efh', 'einfamilienhaus', 'flachdach', 'flach', 'bauhaus', 'gebäudetyp', 'rect'], length: 11, width: 9, height: 6, pitch: 3, cover: 'kunststoff', attika: 0.4 }),
  verfFlach({ id: 'bungalow-flach', name: 'Bungalow Flachdach', kurz: 'Eingeschossiger Bungalow mit Flachdach (breit gelagert).', schlagworte: ['bungalow', 'flachdach', 'flach', 'eingeschossig', 'gebäudetyp', 'rect'], length: 14, width: 11, height: 3.2, pitch: 2.5, cover: 'kunststoff', attika: 0.3 }),
  verfSattel({ id: 'mfh-sattel', name: 'Mehrfamilienhaus Satteldach', kurz: 'Mehrfamilienhaus (ein Baukörper) mit Satteldach.', schlagworte: ['mfh', 'mehrfamilienhaus', 'satteldach', 'sattel', 'gebäudetyp', 'schrägdach'], length: 18, width: 12, height: 9, pitch: 35 }),
  verfFlach({ id: 'mfh-flach', name: 'Mehrfamilienhaus Flachdach', kurz: 'Mehrfamilienhaus (ein Baukörper) mit Flachdach und Attika.', schlagworte: ['mfh', 'mehrfamilienhaus', 'flachdach', 'flach', 'gebäudetyp', 'rect'], length: 20, width: 14, height: 11, pitch: 2.5, cover: 'kunststoff', attika: 0.5 }),
  verfWalm({ id: 'mfh-walm', name: 'Mehrfamilienhaus Walmdach', kurz: 'Mehrfamilienhaus (ein Baukörper) mit Walmdach (Länge > Breite).', schlagworte: ['mfh', 'mehrfamilienhaus', 'walmdach', 'walm', 'gebäudetyp', 'schrägdach'], length: 20, width: 13, height: 9, pitch: 28 }),
  verfFlach({ id: 'gewerbehalle-flach', name: 'Gewerbehalle Flachdach', kurz: 'Gewerbehalle mit großflächigem Flachdach.', schlagworte: ['gewerbehalle', 'gewerbe', 'halle', 'flachdach', 'flach', 'gebäudetyp', 'rect'], length: 30, width: 18, height: 7, pitch: 2.5, cover: 'kunststoff', attika: 0.4 }),
  verfFlach({ id: 'industriehalle-flach', name: 'Industriehalle Flachdach', kurz: 'Industriehalle mit sehr großem Flachdach (hohe Spannweiten, Tragwerk separat).', schlagworte: ['industriehalle', 'industrie', 'halle', 'flachdach', 'flach', 'gebäudetyp', 'rect'], length: 48, width: 24, height: 8, pitch: 2, cover: 'kunststoff', attika: 0.4 }),
  verfSattel({ id: 'scheune-sattel', name: 'Scheune Satteldach', kurz: 'Landwirtschaftliche Scheune mit großem Satteldach (Trapezblech).', schlagworte: ['scheune', 'landwirtschaft', 'satteldach', 'sattel', 'gebäudetyp', 'schrägdach'], length: 20, width: 12, height: 5, pitch: 22, cover: 'trapezblech', battenDist: 40, dd: { rdnGrad: 18, mindestneigungGrad: 5 } }),
  verfPult({ id: 'landwirtschaftliche-halle-pult', name: 'Landwirtschaftliche Halle Pultdach', kurz: 'Landwirtschaftliche Halle mit einseitigem Pultdach (Trapezblech).', schlagworte: ['landwirtschaft', 'halle', 'pultdach', 'pult', 'gebäudetyp', 'schrägdach'], length: 28, width: 14, height: 5.5, pitch: 12, cover: 'trapezblech', battenDist: 40, dd: { rdnGrad: 8, mindestneigungGrad: 5 } }),
  verfFlach({ id: 'werkstatt-flach', name: 'Werkstattgebäude Flachdach', kurz: 'Werkstattgebäude mit Flachdach (kompakter Gewerbebau).', schlagworte: ['werkstatt', 'werkstattgebäude', 'flachdach', 'flach', 'gebäudetyp', 'rect'], length: 18, width: 12, height: 5, pitch: 2.5, cover: 'kunststoff', attika: 0.3 }),
  verfFlach({ id: 'buero-flach', name: 'Bürogebäude Flachdach', kurz: 'Bürogebäude mit Flachdach und umlaufender Attika.', schlagworte: ['büro', 'buero', 'bürogebäude', 'flachdach', 'flach', 'gebäudetyp', 'rect'], length: 22, width: 14, height: 10, pitch: 2.5, cover: 'kunststoff', attika: 0.5 }),
  verfPult({ id: 'carport-pult', name: 'Carport Pultdach', kurz: 'Freistehendes Carport als Pultdach (offene Konstruktion).', schlagworte: ['carport', 'pult', 'pultdach', 'nebengebäude', 'gebäudetyp', 'schrägdach'], length: 6, width: 5, height: 2.6, pitch: 10, cover: 'trapezblech', battenDist: 40, dd: { rdnGrad: 8, mindestneigungGrad: 5 } }),
  verfFlach({ id: 'garage-flach', name: 'Garage Flachdach', kurz: 'Einzelgarage mit Flachdach (Kunststoffabdichtung).', schlagworte: ['garage', 'flachdach', 'flach', 'nebengebäude', 'gebäudetyp', 'rect'], length: 6, width: 3, height: 2.6, pitch: 2, cover: 'kunststoff', attika: 0.2 }),
  verfSattel({ id: 'garage-sattel', name: 'Garage Satteldach', kurz: 'Einzelgarage mit kleinem Satteldach (Ziegel).', schlagworte: ['garage', 'satteldach', 'sattel', 'nebengebäude', 'gebäudetyp', 'schrägdach'], length: 6, width: 3, height: 2.6, pitch: 30 }),
];

// =====================================================================
// 7c. ERWEITERTE BIBLIOTHEK (additiv) — geplante Vorlagen
// =====================================================================
// Engine baut diese Formen NICHT sauber (Compound-Bug bei L/T/U, kein Build-Pfad
// für Sonderformen, Mehrkörper = mehrere Baukörper). Sichtbar, aber nicht
// anwendbar (apply undefined, geplantGrund Pflicht).
const GEPLANT_NEU: DachformVorlage[] = [
  // — WALM+SONDER (zusätzlich) —
  geplanteVorlage({ id: 'pyramidendach', name: 'Pyramidendach', shapeKey: 'pyramidendach', category: 'pitched', kurz: 'Vier gleich geneigte Dreiecke über quadratischem Grundriss, die in einer Spitze zusammenlaufen.', grund: 'Wie das Zeltdach ohne Build-Pfad in updateBuilding; nur quadratischer Grundriss mit gleicher Neigung wäre konsistent — sonst entsteht still falsche Geometrie. Geplant.', schlagworte: ['pyramidendach', 'pyramide', 'zelt', 'spitze', 'grat', 'schrägdach'], geom: { length: 9, width: 9, height: 5, pitch: 32 }, flags: { ...FLAGS_WALM, firstpfette: false }, dachstuhltyp: 'Pyramidendach (4 Gratsparren, ohne First — geplant)' }),

  // — L/T/U-Grundrisse (alle geplant) —
  geplanteVorlage({ id: 'sattel-u', name: 'Satteldach U-Grundriss', shapeKey: 'u-grundriss', category: 'pitched', kurz: 'Satteldächer auf U-förmigem Grundriss mit mehreren Kehlen.', grund: 'U-Grundriss erbt den buildCompoundPitched-Überlappungsbug; Mehrfach-Kehlen/Schiftung sind nicht stimmig — geplant.', schlagworte: ['u-form', 'u-grundriss', 'satteldach', 'sattel', 'kehle', 'schrägdach'], geom: { length: 16, width: 12, height: 5, pitch: 35 }, dachstuhltyp: 'Sattel-Tragwerk U-Grundriss mit Mehrfach-Kehlen (geplant)' }),
  geplanteVorlage({ id: 'walm-l', name: 'Walmdach L-Grundriss', shapeKey: 'l-shape', category: 'pitched', kurz: 'Walmdächer auf L-förmigem Grundriss (Grat- und Kehlbereiche an der einspringenden Ecke).', grund: 'L-Grundriss nutzt den fehlerhaften Compound-Pfad; Walm-Grate/Kehlen an der Ecke sind nicht stimmig — geplant.', schlagworte: ['l-form', 'l-shape', 'walmdach', 'walm', 'kehle', 'grat', 'schrägdach'], geom: { length: 14, width: 9, height: 5, pitch: 30 }, flags: FLAGS_WALM, dachstuhltyp: 'Walm-Tragwerk L-Grundriss (geplant)' }),
  geplanteVorlage({ id: 'walm-t', name: 'Walmdach T-Grundriss', shapeKey: 't-shape', category: 'pitched', kurz: 'Walmdächer auf T-förmigem Grundriss mit zwei Kehlbereichen.', grund: 'T-Grundriss erbt den fehlerhaften Compound-Pfad — geplant.', schlagworte: ['t-form', 't-shape', 'walmdach', 'walm', 'kehle', 'schrägdach'], geom: { length: 14, width: 9, height: 5, pitch: 30 }, flags: FLAGS_WALM, dachstuhltyp: 'Walm-Tragwerk T-Grundriss (geplant)' }),
  geplanteVorlage({ id: 'walm-u', name: 'Walmdach U-Grundriss', shapeKey: 'u-grundriss', category: 'pitched', kurz: 'Walmdächer auf U-förmigem Grundriss mit mehreren Graten und Kehlen.', grund: 'U-Grundriss erbt den Compound-Bug; Mehrfach-Kehlen sind nicht umgesetzt — geplant.', schlagworte: ['u-form', 'u-grundriss', 'walmdach', 'walm', 'kehle', 'grat', 'schrägdach'], geom: { length: 16, width: 12, height: 5, pitch: 30 }, flags: FLAGS_WALM, dachstuhltyp: 'Walm-Tragwerk U-Grundriss (geplant)' }),
  // flach-t und flach-u sind jetzt VERFÜGBAR (echte T-/U-Flachdach-Polygone) -> siehe VERFUEGBAR_GRUNDRISS.
  geplanteVorlage({ id: 'pult-l', name: 'Pultdach L-Grundriss', shapeKey: 'l-shape', category: 'pitched', kurz: 'Pultdächer auf L-förmigem Grundriss (Anschluss zweier Pultflächen).', grund: 'L-Grundriss-Pultkombination hat keinen sauberen Build-Pfad (Compound) — geplant.', schlagworte: ['l-form', 'l-shape', 'pultdach', 'pult', 'schrägdach'], geom: { length: 14, width: 9, height: 5, pitch: 15 }, flags: FLAGS_PULT, dachstuhltyp: 'Pult-Tragwerk L-Grundriss (geplant)' }),
  geplanteVorlage({ id: 'pult-t', name: 'Pultdach T-Grundriss', shapeKey: 't-shape', category: 'pitched', kurz: 'Pultdächer auf T-förmigem Grundriss.', grund: 'T-Grundriss-Pultkombination hat keinen sauberen Build-Pfad (Compound) — geplant.', schlagworte: ['t-form', 't-shape', 'pultdach', 'pult', 'schrägdach'], geom: { length: 14, width: 9, height: 5, pitch: 15 }, flags: FLAGS_PULT, dachstuhltyp: 'Pult-Tragwerk T-Grundriss (geplant)' }),
  geplanteVorlage({ id: 'pult-u', name: 'Pultdach U-Grundriss', shapeKey: 'u-grundriss', category: 'pitched', kurz: 'Pultdächer auf U-förmigem Grundriss.', grund: 'U-Grundriss-Pultkombination hat keinen sauberen Build-Pfad (Compound) — geplant.', schlagworte: ['u-form', 'u-grundriss', 'pultdach', 'pult', 'schrägdach'], geom: { length: 16, width: 12, height: 5, pitch: 15 }, flags: FLAGS_PULT, dachstuhltyp: 'Pult-Tragwerk U-Grundriss (geplant)' }),

  // — Sonder-Gebäudeformen mit zusammengesetztem Grundriss (geplant) —
  geplanteVorlage({ id: 'winkelbungalow-l', name: 'Winkelbungalow L-Form', shapeKey: 'l-shape', category: 'pitched', kurz: 'Eingeschossiger Winkelbungalow mit L-förmigem Grundriss und Walm-/Kehlbereichen.', grund: 'L-Grundriss erbt den Compound-Bug; Kehle/Schiftung am Winkel nicht stimmig — geplant.', schlagworte: ['winkelbungalow', 'bungalow', 'l-form', 'l-shape', 'kehle', 'gebäudetyp', 'schrägdach'], geom: { length: 15, width: 10, height: 3.2, pitch: 25 }, flags: FLAGS_WALM, dachstuhltyp: 'Winkelbungalow L-Grundriss (geplant)' }),
  geplanteVorlage({ id: 'hofgebaeude-u', name: 'Hofgebäude U-Form', shapeKey: 'u-grundriss', category: 'pitched', kurz: 'Dreiflügeliges Hofgebäude mit U-förmigem Grundriss um einen Innenhof.', grund: 'U-Grundriss mit mehreren Kehlen/Graten erbt den Compound-Bug — geplant.', schlagworte: ['hofgebäude', 'hof', 'u-form', 'u-grundriss', 'dreiflügel', 'kehle', 'schrägdach'], geom: { length: 18, width: 14, height: 5, pitch: 30 }, flags: FLAGS_WALM, dachstuhltyp: 'Hofgebäude U-Grundriss (geplant)' }),
  geplanteVorlage({ id: 'mehrfluegelhaus-u', name: 'Mehrflügelhaus U-Form', shapeKey: 'u-grundriss', category: 'pitched', kurz: 'Mehrflügeliges Wohnhaus mit U-förmigem Grundriss.', grund: 'Mehrere Flügel/Kehlen erben den Compound-Bug — geplant.', schlagworte: ['mehrflügelhaus', 'mehrflügel', 'u-form', 'u-grundriss', 'flügel', 'kehle', 'schrägdach'], geom: { length: 20, width: 16, height: 5, pitch: 35 }, flags: FLAGS_WALM, dachstuhltyp: 'Mehrflügelhaus U-Grundriss (geplant)' }),

  // — Anbauten / Mehrkörper (geplant) —
  geplanteVorlage({ id: 'anbau-links', name: 'Anbau links', shapeKey: 'mehrkoerper', category: 'pitched', kurz: 'Hauptgebäude mit linksseitigem Anbau (zweiter Baukörper).', grund: 'Mehrkörper (Haupthaus + Anbau) ist kein einzelner Build-Pfad; zweiter Baukörper/Anschluss nicht umgesetzt — geplant.', schlagworte: ['anbau', 'anbau links', 'mehrkörper', 'mehrkoerper', 'nebenkörper', 'schrägdach'], geom: { length: 10, width: 8, height: 5, pitch: 35 }, dachstuhltyp: 'Haupthaus + Anbau links (geplant)' }),
  geplanteVorlage({ id: 'anbau-rechts', name: 'Anbau rechts', shapeKey: 'mehrkoerper', category: 'pitched', kurz: 'Hauptgebäude mit rechtsseitigem Anbau (zweiter Baukörper).', grund: 'Mehrkörper-Geometrie (Haupthaus + Anbau) nicht als Build-Pfad umgesetzt — geplant.', schlagworte: ['anbau', 'anbau rechts', 'mehrkörper', 'mehrkoerper', 'nebenkörper', 'schrägdach'], geom: { length: 10, width: 8, height: 5, pitch: 35 }, dachstuhltyp: 'Haupthaus + Anbau rechts (geplant)' }),
  geplanteVorlage({ id: 'anbau-rueckseitig', name: 'Anbau rückseitig', shapeKey: 'mehrkoerper', category: 'pitched', kurz: 'Hauptgebäude mit rückseitigem Anbau (Querbaukörper).', grund: 'Rückseitiger Querbaukörper bildet eine Kehle und ist als Mehrkörper nicht umgesetzt — geplant.', schlagworte: ['anbau', 'anbau rückseitig', 'mehrkörper', 'mehrkoerper', 'kehle', 'schrägdach'], geom: { length: 10, width: 8, height: 5, pitch: 35 }, dachstuhltyp: 'Haupthaus + rückseitiger Anbau (geplant)' }),
  geplanteVorlage({ id: 'hauptgebaeude-garage', name: 'Hauptgebäude mit Garage', shapeKey: 'mehrkoerper', category: 'pitched', kurz: 'Wohnhaus mit angebauter Garage (zweiter, niedrigerer Baukörper).', grund: 'Zwei getrennte Baukörper (Haus + Garage) sind kein einzelner Build-Pfad — geplant.', schlagworte: ['hauptgebäude', 'garage', 'mehrkörper', 'mehrkoerper', 'nebengebäude', 'schrägdach'], geom: { length: 10, width: 8, height: 5, pitch: 35 }, dachstuhltyp: 'Wohnhaus + angebaute Garage (geplant)' }),
  geplanteVorlage({ id: 'hauptgebaeude-carport', name: 'Hauptgebäude mit Carport', shapeKey: 'mehrkoerper', category: 'pitched', kurz: 'Wohnhaus mit angebautem Carport (offener Nebenkörper).', grund: 'Haus + Carport sind zwei Baukörper ohne einzelnen Build-Pfad — geplant.', schlagworte: ['hauptgebäude', 'carport', 'mehrkörper', 'mehrkoerper', 'nebengebäude', 'schrägdach'], geom: { length: 10, width: 8, height: 5, pitch: 35 }, dachstuhltyp: 'Wohnhaus + angebauter Carport (geplant)' }),
  geplanteVorlage({ id: 'hauptgebaeude-nebengebaeude', name: 'Hauptgebäude mit Nebengebäude', shapeKey: 'mehrkoerper', category: 'pitched', kurz: 'Wohnhaus mit separatem Nebengebäude (zweiter Baukörper).', grund: 'Haupt- und Nebengebäude sind zwei Baukörper ohne einzelnen Build-Pfad — geplant.', schlagworte: ['hauptgebäude', 'nebengebäude', 'mehrkörper', 'mehrkoerper', 'schrägdach'], geom: { length: 10, width: 8, height: 5, pitch: 35 }, dachstuhltyp: 'Wohnhaus + Nebengebäude (geplant)' }),

  // — PV-Mehrkörper (geplant) —
  geplanteVorlage({ id: 'pv-anbau', name: 'Anbau (PV)', shapeKey: 'mehrkoerper', category: 'pitched', kurz: 'PV auf Haupthaus und Anbau — Mehrkörper-Belegung über zwei Baukörper.', grund: 'PV-Belegung über zwei getrennte Baukörper setzt eine Mehrkörper-Geometrie voraus, die nicht umgesetzt ist — geplant.', schlagworte: ['anbau', 'pv', 'photovoltaik', 'mehrkörper', 'mehrkoerper', 'schrägdach'], geom: { length: 10, width: 8, height: 5, pitch: 30 }, dachstuhltyp: 'Haupthaus + Anbau, PV-Mehrkörper (geplant)' }),

  // — Mehrkörper-Gebäudetypen (geplant) —
  geplanteVorlage({ id: 'doppelhaus-sattel', name: 'Doppelhaus Satteldach', shapeKey: 'mehrkoerper', category: 'pitched', kurz: 'Doppelhaus (zwei gespiegelte Haushälften) mit durchlaufendem Satteldach.', grund: 'Zwei Haushälften = Mehrkörper; getrennte/gespiegelte Baukörper sind kein einzelner Build-Pfad — geplant.', schlagworte: ['doppelhaus', 'satteldach', 'sattel', 'mehrkörper', 'mehrkoerper', 'gebäudetyp', 'schrägdach'], geom: { length: 14, width: 9, height: 6, pitch: 38 }, dachstuhltyp: 'Doppelhaus, durchlaufendes Satteldach (geplant)' }),
  geplanteVorlage({ id: 'reihenhaus-sattel', name: 'Reihenhaus Satteldach', shapeKey: 'mehrkoerper', category: 'pitched', kurz: 'Reihenhauszeile (mehrere Häuser) mit durchlaufendem Satteldach.', grund: 'Reihenhauszeile = mehrere Baukörper; nicht als einzelner Build-Pfad umgesetzt — geplant.', schlagworte: ['reihenhaus', 'satteldach', 'sattel', 'mehrkörper', 'mehrkoerper', 'zeile', 'gebäudetyp', 'schrägdach'], geom: { length: 24, width: 9, height: 6, pitch: 38 }, dachstuhltyp: 'Reihenhauszeile, durchlaufendes Satteldach (geplant)' }),
  geplanteVorlage({ id: 'gewerbehalle-shed', name: 'Gewerbehalle Sheddach', shapeKey: 'sheddach', category: 'pitched', kurz: 'Gewerbehalle mit Sheddach (Sägezahn, Nordlichtbänder).', grund: 'Sheddach (Reihung asymmetrischer Pulte mit Nordlicht) hat keinen Build-Pfad — geplant.', schlagworte: ['gewerbehalle', 'gewerbe', 'halle', 'sheddach', 'sägezahn', 'saegezahn', 'nordlicht', 'schrägdach'], geom: { length: 30, width: 18, height: 6, pitch: 30 }, dachstuhltyp: 'Sägezahn-Binderreihe (geplant)' }),
];

// =====================================================================
// 7d. VERFÜGBARE FLACHDACH-GRUNDRISSE (L/T/U) — Eingabeaufforderung 13
// buildFlat erzeugt ein echtes Grundrisspolygon (keine Doppelzählung, keine Rechteck-Ersatzform).
// Geneigte L/T/U bleiben GEPLANT (Dachverschneidungen/Kehlen noch nicht sicher).
// =====================================================================
const VERFUEGBAR_GRUNDRISS: DachformVorlage[] = [
  verfFlachGrundriss({ id: 'l-shape-flat', name: 'L-Form Flachdach', kurz: 'L-förmiges Flachdach als echtes Grundrisspolygon (einspringender Innenwinkel, Fläche ohne Doppelzählung).', schlagworte: ['l-form', 'l-shape', 'flachdach', 'flach', 'grundriss', 'innenwinkel'], shapeKey: 'l-shape', engineShape: 'l-shape', length: 12, width: 8, lengthB: 5, widthB: 4, height: 5, innenwinkel: 1, innenhof: false, kehleHinweis: 'Innenwinkel wasserführend kritisch — Entwässerung/Gefälle in den einspringenden Bereich planen.' }),
  verfFlachGrundriss({ id: 'flach-t', name: 'Flachdach T-Grundriss', kurz: 'T-förmiges Flachdach als echtes Grundrisspolygon (zwei Innenwinkel, Fläche ohne Doppelzählung).', schlagworte: ['t-form', 't-shape', 'flachdach', 'flach', 'grundriss', 'innenwinkel'], shapeKey: 't-shape', engineShape: 't-shape', length: 14, width: 10, lengthB: 5, widthB: 4, height: 5, innenwinkel: 2, innenhof: false, kehleHinweis: 'Zwei einspringende Bereiche — Entwässerung/Gefälle je Innenwinkel planen.' }),
  verfFlachGrundriss({ id: 'flach-u', name: 'Flachdach U-Grundriss', kurz: 'U-förmiges Flachdach mit Innenhof als echtes Grundrisspolygon (Fläche ohne Doppelzählung der Flügel).', schlagworte: ['u-form', 'u-grundriss', 'flachdach', 'flach', 'innenhof', 'grundriss', 'innenwinkel'], shapeKey: 'u-grundriss', engineShape: 'u-shape', length: 16, width: 12, lengthB: 4, widthB: 4, height: 5, innenwinkel: 2, innenhof: true, kehleHinweis: 'Innenhof + zwei Innenwinkel — Entwässerung des Hofbereichs und der einspringenden Ecken planen.' }),
  // Erste Zielgruppe: Gebäudetypen mit Flachdach auf L-/U-Grundriss
  verfFlachGrundriss({ id: 'winkelbungalow-l-flach', name: 'Winkelbungalow L-Form (Flachdach)', kurz: 'Eingeschossiger Winkelbungalow, L-Grundriss mit Flachdach (echtes Polygon, einspringender Winkel).', schlagworte: ['winkelbungalow', 'bungalow', 'l-form', 'l-shape', 'flachdach', 'flach', 'gebäudetyp', 'grundriss'], shapeKey: 'l-shape', engineShape: 'l-shape', length: 15, width: 10, lengthB: 6, widthB: 5, height: 3.2, innenwinkel: 1, innenhof: false, kehleHinweis: 'Winkelbereich wasserführend — Gefälle zum Ablauf im Innenwinkel planen.', dachstuhltyp: 'Eingeschossiger Winkelbungalow, L-Grundriss (Flachdach)' }),
  verfFlachGrundriss({ id: 'gewerbe-l-flach', name: 'Gewerbegebäude L-Form (Flachdach)', kurz: 'Gewerbegebäude mit L-förmigem Flachdach (große zusammengesetzte Dachfläche, echtes Polygon).', schlagworte: ['gewerbe', 'gewerbegebäude', 'l-form', 'l-shape', 'flachdach', 'flach', 'gebäudetyp', 'grundriss'], shapeKey: 'l-shape', engineShape: 'l-shape', length: 24, width: 16, lengthB: 10, widthB: 8, height: 6, innenwinkel: 1, innenhof: false, kehleHinweis: 'Große Innenwinkel-Kehle — Entwässerung/Notüberläufe planen.', dachstuhltyp: 'Gewerbe-Tragdecke L-Grundriss (Flachdach)' }),
  verfFlachGrundriss({ id: 'buero-u-flach', name: 'Bürogebäude U-Form (Flachdach)', kurz: 'Bürogebäude mit U-förmigem Flachdach und Innenhof (echtes Polygon, zwei Innenwinkel).', schlagworte: ['büro', 'buero', 'bürogebäude', 'u-form', 'u-grundriss', 'flachdach', 'flach', 'innenhof', 'gebäudetyp', 'grundriss'], shapeKey: 'u-grundriss', engineShape: 'u-shape', length: 22, width: 14, lengthB: 6, widthB: 6, height: 10, innenwinkel: 2, innenhof: true, kehleHinweis: 'Innenhof-Entwässerung + zwei Innenwinkel — Abläufe/Notüberläufe je Bereich planen.', dachstuhltyp: 'Büro-Tragdecke U-Grundriss (Flachdach)' }),
];

// =====================================================================
// 7e. GAUBEN-BIBLIOTHEK (Eingabeaufforderung 14/15) — Gaubenarten als EIGENE Vorlagen.
// Unterstützte Arten (Schlepp/Giebel/Satteldach/Walm/Flach) -> verfügbar; anzeigeStatus 'teilweise'
// (schematische Gaube als Aufbau, keine echte Gaubendach-Geometrie). Seltene Arten (Spitz/Dreieck/
// Tonne/Segmentbogen/Fledermaus/Rund/Zwerch) sowie Gauben auf (noch) nicht baubaren Hauptdächern
// (Mansard/Krüppelwalm/Mehrkörper) -> 'geplant' (eigene Bildvorschau, NICHT als Obstacle gesetzt).
// =====================================================================
const GAUBEN_NEU: DachformVorlage[] = [
  // — Satteldach, unterstützte Gaubenarten (verfügbar / teilweise) —
  verfSattel({ id: 'sattel-schleppgaube-3', name: 'Satteldach mit drei Schleppgauben', kurz: 'Satteldach mit drei gleichmäßig verteilten Schleppgauben (schematische Aufbauten, kollisionsfrei platziert).', schlagworte: ['satteldach', 'sattel', 'gaube', 'schleppgaube', 'schlepp', 'drei', 'mehrere', 'aufbau', 'schrägdach'], length: 14, width: 8, height: 5, pitch: 42 }),
  verfSattel({ id: 'sattel-satteldachgaube-2', name: 'Satteldach mit zwei Satteldachgauben', kurz: 'Satteldach mit zwei Satteldach-/Giebelgauben (eigener kleiner First, schematisch).', schlagworte: ['satteldach', 'sattel', 'gaube', 'satteldachgaube', 'giebelgaube', 'zwei', 'aufbau', 'schrägdach'], length: 12, width: 8, height: 5, pitch: 42 }),
  verfSattel({ id: 'sattel-gaubenband', name: 'Satteldach mit Gaubenband', kurz: 'Satteldach mit durchlaufendem Gaubenband (mehrere gleichartige Schleppgauben in einer Reihe).', schlagworte: ['satteldach', 'sattel', 'gaube', 'schleppgaube', 'schlepp', 'gaubenband', 'band', 'aufbau', 'schrägdach'], length: 16, width: 8, height: 5, pitch: 40 }),
  verfSattel({ id: 'sattel-gaube-pv', name: 'Satteldach mit Gaube und PV-Sperrflächen', kurz: 'Satteldach mit Giebelgaube; PV-Belegung mit Sperrfläche um die Gaube (Verschattung beachten).', schlagworte: ['satteldach', 'sattel', 'gaube', 'giebelgaube', 'pv', 'photovoltaik', 'sperrfläche', 'aufbau', 'schrägdach'], length: 11, width: 8, height: 5, pitch: 38, pv: { belegbareSeiten: ['Hauptdach Süd (um Gaube/Verschattung ausgespart)'], ausgeschlosseneSeiten: ['Nordhang', 'Gaubenzone', 'Verschattungszone'] } }),
  // — Walmdach, unterstützte Gaubenarten —
  verfWalm({ id: 'walm-zwei-gauben', name: 'Walmdach mit zwei Gauben', kurz: 'Walmdach mit zwei Giebelgauben auf der Süd-Hauptfläche (schematisch, Länge > Breite).', schlagworte: ['walmdach', 'walm', 'gaube', 'giebelgaube', 'zwei', 'aufbau', 'schrägdach'], length: 14, width: 9, height: 5, pitch: 32 }),
  verfWalm({ id: 'walm-schleppgaube', name: 'Walmdach mit Schleppgaube', kurz: 'Walmdach mit Schleppgaube auf der Hauptfläche (schematisch).', schlagworte: ['walmdach', 'walm', 'gaube', 'schleppgaube', 'schlepp', 'aufbau', 'schrägdach'], length: 13, width: 9, height: 5, pitch: 32 }),
  verfWalm({ id: 'walm-walmdachgaube', name: 'Walmdach mit Walmdachgaube', kurz: 'Walmdach mit abgewalmter Gaube (Trapezgaube als Näherung der Walmgaube, schematisch).', schlagworte: ['walmdach', 'walm', 'gaube', 'walmgaube', 'walmdachgaube', 'aufbau', 'schrägdach'], length: 13, width: 9, height: 5, pitch: 34 }),
  verfWalm({ id: 'walm-flachgaube', name: 'Walmdach mit Flachdachgaube', kurz: 'Walmdach mit kubischer Flachdachgaube (schematisch).', schlagworte: ['walmdach', 'walm', 'gaube', 'flachgaube', 'flachdachgaube', 'aufbau', 'schrägdach'], length: 13, width: 9, height: 5, pitch: 30 }),
  verfWalm({ id: 'walm-gaube-kamin', name: 'Walmdach mit Gaube und Kamin', kurz: 'Walmdach mit Giebelgaube und Schornstein (Sperrflächen, schematisch).', schlagworte: ['walmdach', 'walm', 'gaube', 'giebelgaube', 'kamin', 'schornstein', 'aufbau', 'schrägdach'], length: 14, width: 9, height: 5, pitch: 32 }),
  // — Pultdach —
  verfPult({ id: 'pult-flachgaube', name: 'Pultdach mit Flachdachgaube', kurz: 'Pultdach mit kubischer Flachdachgaube als Belichtungsaufbau (schematisch).', schlagworte: ['pultdach', 'pult', 'gaube', 'flachgaube', 'flachdachgaube', 'aufbau', 'schrägdach'], length: 10, width: 6, height: 4.5, pitch: 22 }),
  // — Gebäudetypen mit Gauben —
  verfSattel({ id: 'mfh-gauben', name: 'Mehrfamilienhaus mit Gauben', kurz: 'Mehrfamilienhaus-Satteldach mit mehreren Schleppgauben (Wohnraumbelichtung, schematisch).', schlagworte: ['mfh', 'mehrfamilienhaus', 'satteldach', 'sattel', 'gaube', 'schleppgaube', 'schlepp', 'mehrere', 'gebäudetyp', 'aufbau', 'schrägdach'], length: 18, width: 12, height: 9, pitch: 38 }),
  verfWalm({ id: 'stadtvilla-gauben', name: 'Stadtvilla mit Gauben', kurz: 'Walmdach-Stadtvilla mit zwei Giebelgauben (repräsentativ, schematisch).', schlagworte: ['stadtvilla', 'walmdach', 'walm', 'gaube', 'giebelgaube', 'zwei', 'gebäudetyp', 'aufbau', 'schrägdach'], length: 14, width: 11, height: 6, pitch: 28 }),

  // — Seltene Gaubenarten: eigene Bildvorschau, aber als Obstacle NICHT abbildbar -> geplant —
  geplanteVorlage({ id: 'sattel-spitzgaube', name: 'Satteldach mit Spitzgaube', shapeKey: 'sattel', category: 'pitched', kurz: 'Satteldach mit spitzer, hoher Dreiecks-/Spitzgaube.', grund: 'Spitzgaube ist als Bildvorschau vorbereitet; eine passende Aufbau-Geometrie wird noch nicht gesetzt — geplant.', schlagworte: ['satteldach', 'sattel', 'gaube', 'spitzgaube', 'spitz', 'aufbau', 'schrägdach'], geom: { length: 10, width: 8, height: 5, pitch: 45 }, flags: FLAGS_SATTEL_STEIL }),
  geplanteVorlage({ id: 'sattel-dreiecksgaube', name: 'Satteldach mit Dreiecksgaube', shapeKey: 'sattel', category: 'pitched', kurz: 'Satteldach mit dreieckiger Gaube (Dreiecksgaube).', grund: 'Dreiecksgaube ist als Bildvorschau vorbereitet; Aufbau-Geometrie folgt — geplant.', schlagworte: ['satteldach', 'sattel', 'gaube', 'dreiecksgaube', 'dreieck', 'spitz', 'aufbau', 'schrägdach'], geom: { length: 10, width: 8, height: 5, pitch: 45 }, flags: FLAGS_SATTEL_STEIL }),
  geplanteVorlage({ id: 'sattel-tonnengaube', name: 'Satteldach mit Tonnengaube', shapeKey: 'sattel', category: 'pitched', kurz: 'Satteldach mit gerundeter Tonnengaube (Bogenform).', grund: 'Gerundete Tonnengaube ist nur als Bildvorschau vorbereitet; gekrümmte Aufbau-Geometrie noch nicht gesetzt — geplant.', schlagworte: ['satteldach', 'sattel', 'gaube', 'tonnengaube', 'tonne', 'rund', 'aufbau', 'schrägdach'], geom: { length: 11, width: 8, height: 5, pitch: 40 } }),
  geplanteVorlage({ id: 'sattel-segmentbogengaube', name: 'Satteldach mit Segmentbogengaube', shapeKey: 'sattel', category: 'pitched', kurz: 'Satteldach mit flach gerundeter Segmentbogengaube.', grund: 'Segmentbogengaube ist nur als Bildvorschau vorbereitet; gekrümmte Geometrie folgt — geplant.', schlagworte: ['satteldach', 'sattel', 'gaube', 'segmentbogengaube', 'segmentbogen', 'tonne', 'aufbau', 'schrägdach'], geom: { length: 11, width: 8, height: 5, pitch: 40 } }),
  geplanteVorlage({ id: 'sattel-fledermausgaube', name: 'Satteldach mit Fledermausgaube', shapeKey: 'sattel', category: 'pitched', kurz: 'Satteldach mit weich geschwungener Fledermausgaube.', grund: 'Fledermausgaube (geschwungene Ogee-Form) ist nur als Bildvorschau vorbereitet; freie Geometrie noch nicht umsetzbar — geplant.', schlagworte: ['satteldach', 'sattel', 'gaube', 'fledermausgaube', 'fledermaus', 'aufbau', 'schrägdach'], geom: { length: 12, width: 8, height: 5, pitch: 40 } }),
  geplanteVorlage({ id: 'sattel-rundgaube', name: 'Satteldach mit Rundgaube', shapeKey: 'sattel', category: 'pitched', kurz: 'Satteldach mit halbrunder Rundgaube (Ochsenauge-nah).', grund: 'Rundgaube ist nur als Bildvorschau vorbereitet; runde Aufbau-Geometrie folgt — geplant.', schlagworte: ['satteldach', 'sattel', 'gaube', 'rundgaube', 'rund', 'aufbau', 'schrägdach'], geom: { length: 10, width: 8, height: 5, pitch: 42 } }),
  geplanteVorlage({ id: 'sattel-zwerchgaube', name: 'Satteldach mit Zwerchgaube', shapeKey: 'sattel', category: 'pitched', kurz: 'Satteldach mit wandbündiger Zwerchgaube (eigenes Giebeldach bis zur Traufe).', grund: 'Zwerchgaube (wandbündig, eigener Giebel bis Traufe) braucht eine eigene Baukörper-/Dachverschneidung — als Vorschau vorbereitet, geplant.', schlagworte: ['satteldach', 'sattel', 'gaube', 'zwerchgaube', 'zwerch', 'aufbau', 'schrägdach'], geom: { length: 12, width: 9, height: 6, pitch: 40 } }),
  geplanteVorlage({ id: 'sattel-gaube-wangen', name: 'Satteldach mit Gaube (seitliche Wangen, Detail)', shapeKey: 'sattel', category: 'pitched', kurz: 'Giebelgaube mit ausgeprägten seitlichen Wangen und kleinem Dachüberstand (Detailvariante).', grund: 'Die Detailausbildung (echte Wangen-/Überstand-Geometrie der Gaube) ist als Vorschau vorbereitet; nur als schematischer Aufbau noch nicht vollständig — geplant.', schlagworte: ['satteldach', 'sattel', 'gaube', 'giebelgaube', 'wangen', 'überstand', 'aufbau', 'schrägdach'], geom: { length: 11, width: 8, height: 5, pitch: 42 } }),

  // — Gauben auf (noch) nicht baubaren Hauptdächern -> geplant —
  geplanteVorlage({ id: 'mansard-schleppgauben', name: 'Mansarddach mit Schleppgauben', shapeKey: 'mansard', category: 'pitched', kurz: 'Mansarddach mit Schleppgauben im steilen unteren Mansardteil.', grund: 'Mansarddach selbst ist noch nicht baubar (Doppelneigung/Knickpfette) — Gaube nur als Vorschau, geplant.', schlagworte: ['mansarddach', 'mansard', 'gaube', 'schleppgaube', 'schlepp', 'mehrere', 'knick', 'schrägdach'], geom: { length: 12, width: 9, height: 5, pitch: 60 } }),
  geplanteVorlage({ id: 'mansard-mittelgaube', name: 'Mansarddach mit breiter Mittelgaube', shapeKey: 'mansard', category: 'pitched', kurz: 'Mansarddach mit breiter zentraler Zwerch-/Mittelgaube.', grund: 'Mansarddach + breite Mittelgaube ist nur als Vorschau vorbereitet — geplant.', schlagworte: ['mansarddach', 'mansard', 'gaube', 'mittelgaube', 'breit', 'knick', 'schrägdach'], geom: { length: 13, width: 9, height: 5, pitch: 60 } }),
  geplanteVorlage({ id: 'mansardwalm-gauben', name: 'Mansardwalmdach mit Gauben', shapeKey: 'mansardwalm', category: 'pitched', kurz: 'Mansardwalmdach mit Gauben im Steilteil.', grund: 'Mansardwalm + Gauben sind nur als Vorschau vorbereitet (Doppelknick + Walm + Gaube) — geplant.', schlagworte: ['mansardwalm', 'mansard', 'walm', 'gaube', 'giebelgaube', 'mehrere', 'knick', 'schrägdach'], geom: { length: 13, width: 9, height: 5, pitch: 60 }, flags: FLAGS_WALM }),
  geplanteVorlage({ id: 'krueppelwalm-gaube', name: 'Krüppelwalmdach mit Gaube', shapeKey: 'krueppelwalm', category: 'pitched', kurz: 'Krüppelwalmdach mit Giebelgaube.', grund: 'Krüppelwalmdach selbst ist noch nicht baubar (Teilwalm) — Gaube nur als Vorschau, geplant.', schlagworte: ['krüppelwalm', 'krueppelwalm', 'walm', 'gaube', 'giebelgaube', 'schrägdach'], geom: { length: 12, width: 8, height: 5, pitch: 38 }, flags: FLAGS_WALM }),
  geplanteVorlage({ id: 'haus-gaube-garage', name: 'Haus mit Gaube und Garage', shapeKey: 'mehrkoerper', category: 'pitched', kurz: 'Wohnhaus mit Gaube und angebauter Garage (zwei Baukörper).', grund: 'Mehrkörper (Haus + Garage) ist kein einzelner Build-Pfad; Gaube nur als Vorschau — geplant.', schlagworte: ['hauptgebäude', 'haus', 'gaube', 'giebelgaube', 'garage', 'mehrkörper', 'mehrkoerper', 'nebengebäude', 'schrägdach'], geom: { length: 10, width: 8, height: 5, pitch: 38 } }),
];

/** Alle Dachform-Vorlagen (verfügbar + geplant) inkl. erweiterter Bibliothek. */
export const DACHFORM_VORLAGEN: DachformVorlage[] = [...VERFUEGBAR, ...VERFUEGBAR_NEU, ...VERFUEGBAR_GRUNDRISS, ...GAUBEN_NEU, ...GEPLANT, ...GEPLANT_NEU];

export default DACHFORM_VORLAGEN;
