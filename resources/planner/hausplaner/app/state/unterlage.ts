/**
 * AUF-88-P1 — die Referenzunterlage: gelesen, nicht zusammengebaut.
 *
 * **Dieselbe Naht wie `data-objektkopf`, `data-projekte`, `data-rechte`** — das Blade setzt,
 * `main.tsx` liest, der UI-Zustand hält. Kein Lade-Fetch: der aktuelle Stand der Unterlage kommt
 * fertig mit der Seite.
 *
 * **Was hier NICHT passiert: rechnen.** Ob ein Bild erreichbar ist (`bildDa`), ob der Import-
 * Dienst fehlt — das entscheidet `HausplanerController::hausplanerUnterlage()`. Die Insel zeigt,
 * was der Server sagt.
 */

/** Das Attribut am Mount-Knoten (`data-unterlage`). */
export const UNTERLAGE_ATTRIBUT = 'unterlage';

export type UnterlageStatus = 'neu' | 'klassifiziert' | 'verarbeitet' | 'fehler';

export interface AktuelleUnterlage {
  id: number;
  status: UnterlageStatus;
  typ: string | null;
  originalName: string;
  hochgeladenAm: string | null;
  massstabMmProEinheit: number | null;
  /** `null`, solange kein Bild erreichbar ist — kein kaputter Link, sondern ein fehlendes Bild. */
  bildUrl: string | null;
  massstabUrl: string;
  statusUrl: string;
  fehler: string | null;
  /** PDF klassifiziert, aber kein Bild da, weil der Import-Dienst nicht konfiguriert ist (K-06). */
  importDienstNoetig: boolean;
}

export interface UnterlageZustand {
  objektId: number;
  hochladenUrl: string;
  aktuelle: AktuelleUnterlage | null;
}

function istAktuelle(x: unknown): x is AktuelleUnterlage {
  if (typeof x !== 'object' || x === null) return false;
  const o = x as Record<string, unknown>;
  return typeof o.id === 'number'
    && typeof o.status === 'string'
    && typeof o.originalName === 'string'
    && typeof o.massstabUrl === 'string' && o.massstabUrl.length > 0
    && typeof o.statusUrl === 'string' && o.statusUrl.length > 0
    && (o.bildUrl === null || typeof o.bildUrl === 'string')
    && (o.massstabMmProEinheit === null || typeof o.massstabMmProEinheit === 'number')
    && typeof o.importDienstNoetig === 'boolean';
}

function istZustand(x: unknown): x is UnterlageZustand {
  if (typeof x !== 'object' || x === null) return false;
  const o = x as Record<string, unknown>;
  if (typeof o.objektId !== 'number') return false;
  if (typeof o.hochladenUrl !== 'string' || o.hochladenUrl.length === 0) return false;
  if (o.aktuelle === null) return true;
  return istAktuelle(o.aktuelle);
}

/**
 * Die Unterlage aus dem Attribut. Fehlt es, ist es leer oder unlesbar, gilt `null` — dieselbe
 * Richtung wie bei `leseObjektkopf`: ein kaputter Wert darf nie mehr behaupten als ein fehlender.
 */
export function leseUnterlage(roh: string | null | undefined): UnterlageZustand | null {
  if (!roh) return null;
  let geparst: unknown;
  try {
    geparst = JSON.parse(roh);
  } catch {
    return null;
  }
  return istZustand(geparst) ? geparst : null;
}

/** Ein Satz, der sagt, was gerade Sache ist — für den Fall ohne Bild (K-06). */
export function unterlagenHinweis(u: AktuelleUnterlage): string {
  if (u.fehler) return `Fehler: ${u.fehler}`;
  if (u.importDienstNoetig) return 'PDF hochgeladen — die Rasterung braucht den Import-Dienst, der hier nicht konfiguriert ist.';
  if (u.status === 'neu') return 'Wird hochgeladen …';
  if (u.status === 'klassifiziert' && !u.bildUrl) return 'Wird verarbeitet …';
  return '';
}
