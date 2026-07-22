/**
 * Hausplaner — Dach-Vorlagen (P2b-6). Reine Defaults je Dachform für das ANLEGEN.
 *
 * Die Geometrie (Mesh + Flächen) beherrscht alle vier Formen längst; hier wird nur
 * festgelegt, mit welcher sinnvollen Standard-Neigung eine Form beim Zeichnen startet,
 * plus ein Anzeigename fürs Werkzeug. Keine Szene-Mutation, kein Rendern — reine Tabelle.
 */

export type DachForm = 'sattel' | 'walm' | 'pult' | 'flach';

export interface DachVorlage {
  form: DachForm;
  label: string;
  /** Standard-Neigung in Grad beim Anlegen (Flachdach = 0). */
  neigungGrad: number;
}

/** Reihenfolge = Anzeigereihenfolge im Werkzeug (Sattel zuerst = häufigste Form). */
export const DACH_VORLAGEN: readonly DachVorlage[] = [
  { form: 'sattel', label: 'Satteldach', neigungGrad: 35 },
  { form: 'walm', label: 'Walmdach', neigungGrad: 30 },
  { form: 'pult', label: 'Pultdach', neigungGrad: 15 },
  { form: 'flach', label: 'Flachdach', neigungGrad: 0 },
] as const;

/** Vorlage zu einer Form (fällt auf Sattel zurück, falls unbekannt — nie undefined). */
export function dachVorlage(form: DachForm): DachVorlage {
  return DACH_VORLAGEN.find((v) => v.form === form) ?? DACH_VORLAGEN[0];
}

/** Standard-Neigung (Grad) beim Anlegen dieser Form. */
export function dachNeigungDefault(form: DachForm): number {
  return dachVorlage(form).neigungGrad;
}
