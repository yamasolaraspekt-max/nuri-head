/**
 * Dashboard v2.4 (§34 / UI-10) — Prüfungscenter als DATEN.
 *
 * Reine Funktion über dem, was der Store HEUTE hergibt: `letzteAblehnung: string | null`
 * (store/hausplanerStore.ts:34). Mehr ist nicht da — der Store hält genau EINE Meldung, keine
 * Liste, und `CommandAbgelehnt.grund` geht beim Setzen verloren.
 *
 * v2 ändert den Store NICHT (Auftrag §1/§5). Also liefert diese Funktion heute 0 oder 1 Befund.
 * Das ist keine Attrappe, sondern der ehrliche Ausschnitt: was angezeigt wird, ist wahr; was
 * fehlt, wird unter der Liste ausgesprochen (`BEFUNDE_UMFANG`), nicht verschwiegen.
 *
 * OFFENER POSTEN v3 (hier nur dokumentiert, NICHT gebaut): eine echte Befund-Historie mit
 * `grund`, Zeitstempel und Bauteilbezug verlangt eine Store-Änderung (Feld + Command) und ist
 * damit ein eigener Posten außerhalb dieses Auftrags.
 */

/** Schwere eines Befunds. 'fehler' = Aktion wurde abgelehnt; 'hinweis' = Anmerkung ohne Abbruch. */
export type BefundSchwere = 'hinweis' | 'fehler';

export interface Befund {
  id: string;
  /** Der Meldungstext, UNVERÄNDERT aus dem Store — nicht gekürzt, nicht umformuliert. */
  text: string;
  schwere: BefundSchwere;
}

/** Leerzustand — wörtlich (Abnahmekriterium Batch 2, Punkt 10). NICHT „keine Daten". */
export const BEFUNDE_LEER = 'Keine offenen Befunde.';

/** Ehrlicher Umfangs-Hinweis unter der Liste: was hier (noch) NICHT geführt wird. */
export const BEFUNDE_UMFANG =
  'Geführt wird derzeit nur die zuletzt abgelehnte Aktion. Eine Befund-Historie je Bauteil braucht eine Store-Erweiterung und ist ein eigener Posten (v3).';

/** Stabile id des einen Befunds — die Liste summiert nicht auf (Kante 9). */
export const BEFUND_ID_ABLEHNUNG = 'letzte-ablehnung';

/**
 * Befunde aus dem heutigen Store-Zustand.
 *
 * `null` oder eine leere/nur-Leerzeichen-Meldung ⇒ `[]` (Leerzustand). Sonst genau EIN Befund
 * mit dem unveränderten Text. Kein Aufsummieren über die Zeit — der Store hält keine Historie,
 * also darf die Anzeige auch keine vortäuschen (Kante 9).
 */
export function befundeAus(letzteAblehnung: string | null): Befund[] {
  if (letzteAblehnung === null || letzteAblehnung.trim().length === 0) return [];
  return [{ id: BEFUND_ID_ABLEHNUNG, text: letzteAblehnung, schwere: 'fehler' }];
}
