/**
 * Hausplaner — Öffnungs-Typen als Vorlagen (Tür/Fenster). Reine Kataloge mit Standardmaßen
 * (DIN-nah) für die Auswahl BEIM Anlegen. Nach dem Setzen sind die Maße frei überschreibbar
 * (Maßkette/Panel). Keine Szene-Mutation, kein Rendern — reine Tabelle + Lookup.
 */

export type TuerTyp = 'dreh1' | 'dreh2' | 'schiebe' | 'hebeschiebe' | 'falt';
export type FensterTyp = 'fest' | 'dreh' | 'kipp' | 'drehkipp' | 'zweiflg' | 'schiebe' | 'boden';

export interface TypVorlage<T extends string> {
  typ: T;
  label: string;
  /** lichte Breite in mm (Standard-Vorlage). */
  breite: number;
  /** lichte Höhe in mm (Standard-Vorlage). */
  hoehe: number;
  /** nur Fenster: Standard-Brüstungshöhe in mm. */
  bruestung?: number;
}

/** Tür-Vorlagen (Reihenfolge = Anzeigereihenfolge; Drehtür 1-flg. zuerst = häufigste). */
export const TUER_TYPEN: readonly TypVorlage<TuerTyp>[] = [
  { typ: 'dreh1', label: 'Drehtür 1-flügelig', breite: 875, hoehe: 2010 },
  { typ: 'dreh2', label: 'Doppeltür (2-flügelig)', breite: 1500, hoehe: 2010 },
  { typ: 'schiebe', label: 'Schiebetür', breite: 900, hoehe: 2010 },
  { typ: 'hebeschiebe', label: 'Hebe-Schiebe-Tür', breite: 2000, hoehe: 2100 },
  { typ: 'falt', label: 'Falttür', breite: 900, hoehe: 2010 },
] as const;

/** Fenster-Vorlagen (mit Standard-Brüstung; bodentief = 0). */
export const FENSTER_TYPEN: readonly TypVorlage<FensterTyp>[] = [
  { typ: 'drehkipp', label: 'Dreh-Kipp 1-flügelig', breite: 1010, hoehe: 1360, bruestung: 900 },
  { typ: 'zweiflg', label: 'Dreh-Kipp 2-flügelig', breite: 1510, hoehe: 1360, bruestung: 900 },
  { typ: 'dreh', label: 'Drehflügel', breite: 760, hoehe: 1360, bruestung: 900 },
  { typ: 'kipp', label: 'Kippflügel', breite: 760, hoehe: 600, bruestung: 1800 },
  { typ: 'fest', label: 'Festverglasung', breite: 1010, hoehe: 1360, bruestung: 900 },
  { typ: 'schiebe', label: 'Schiebefenster', breite: 1800, hoehe: 1360, bruestung: 900 },
  { typ: 'boden', label: 'Bodentief / französisch', breite: 1010, hoehe: 2180, bruestung: 0 },
] as const;

/** Tür-Vorlage zu einem Typ (Fallback dreh1 — nie undefined). */
export function tuerTyp(typ: TuerTyp): TypVorlage<TuerTyp> {
  return TUER_TYPEN.find((v) => v.typ === typ) ?? TUER_TYPEN[0];
}

/** Fenster-Vorlage zu einem Typ (Fallback drehkipp — nie undefined). */
export function fensterTyp(typ: FensterTyp): TypVorlage<FensterTyp> {
  return FENSTER_TYPEN.find((v) => v.typ === typ) ?? FENSTER_TYPEN[0];
}
