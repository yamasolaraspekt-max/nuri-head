/**
 * Hausplaner — Treppe als ObjectNode (objectType 'stair'): typsichere Brücke zwischen den
 * Treppen-Fachdaten (Lauflinie, Laufbreite, Geschosshöhe, Nutzungsbereich) und dem flachen
 * `ObjectNode.parameters`-Record. So läuft die Treppe durch die BESTEHENDE CRUD (ADD/UPDATE/
 * REMOVE/MOVE_NODE) ohne neue Command-Typen — additiv, keine zweite Datenhaltung.
 *
 * Konvention: alle Treppen-Schlüssel mit Präfix `treppe.`; Zahlen als number, Bereich als string.
 * Rein & bundle-unabhängig (kein three/Konva, keine Szene-Mutation).
 */

export type TreppenBereich = 'wohnung' | 'gebaeude' | 'aussen';

export interface TreppeParams {
  /** Lauflinie (Mittellinie) im Grundriss, mm. */
  startX: number;
  startY: number;
  endX: number;
  endY: number;
  laufbreite: number;
  geschosshoehe: number;
  bereich: TreppenBereich;
  gewuenschteSteigung?: number;
}

const P = 'treppe.';
const BEREICHE: ReadonlySet<string> = new Set(['wohnung', 'gebaeude', 'aussen']);

/** Treppen-Fachdaten → flaches parameters-Record (nur die treppe.*-Schlüssel). */
export function treppeZuParametern(
  t: TreppeParams,
): Record<string, string | number | boolean | null> {
  const rec: Record<string, string | number | boolean | null> = {
    [`${P}startX`]: Math.round(t.startX),
    [`${P}startY`]: Math.round(t.startY),
    [`${P}endX`]: Math.round(t.endX),
    [`${P}endY`]: Math.round(t.endY),
    [`${P}laufbreite`]: Math.round(t.laufbreite),
    [`${P}geschosshoehe`]: Math.round(t.geschosshoehe),
    [`${P}bereich`]: t.bereich,
  };
  if (t.gewuenschteSteigung !== undefined && t.gewuenschteSteigung > 0) {
    rec[`${P}gewuenschteSteigung`] = Math.round(t.gewuenschteSteigung);
  }
  return rec;
}

const zahl = (v: unknown): number | null =>
  typeof v === 'number' && Number.isFinite(v) ? v : null;

/** parameters-Record → Treppen-Fachdaten; null wenn Pflichtfelder fehlen/ungültig. */
export function parametereZuTreppe(
  rec: Record<string, string | number | boolean | null> | undefined | null,
): TreppeParams | null {
  if (!rec) return null;
  const startX = zahl(rec[`${P}startX`]);
  const startY = zahl(rec[`${P}startY`]);
  const endX = zahl(rec[`${P}endX`]);
  const endY = zahl(rec[`${P}endY`]);
  const laufbreite = zahl(rec[`${P}laufbreite`]);
  const geschosshoehe = zahl(rec[`${P}geschosshoehe`]);
  const bereichRoh = rec[`${P}bereich`];
  if (
    startX === null || startY === null || endX === null || endY === null ||
    laufbreite === null || laufbreite <= 0 ||
    geschosshoehe === null || geschosshoehe <= 0
  ) {
    return null;
  }
  const bereich: TreppenBereich =
    typeof bereichRoh === 'string' && BEREICHE.has(bereichRoh)
      ? (bereichRoh as TreppenBereich)
      : 'wohnung';
  const gw = zahl(rec[`${P}gewuenschteSteigung`]);
  const out: TreppeParams = { startX, startY, endX, endY, laufbreite, geschosshoehe, bereich };
  if (gw !== null && gw > 0) out.gewuenschteSteigung = gw;
  return out;
}
