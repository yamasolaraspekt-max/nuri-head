/**
 * Hausplaner — Wandaufbau / U-Wert (Konfigurator „Wandaufbau" §11, autark; speist Heizlast & Dach).
 *
 * Reine Bauphysik: Wärmedurchgangskoeffizient U aus geschichtetem Aufbau (DIN EN ISO 6946).
 * U = 1 / (Rsi + Σ d/λ + Rse). Keine Szene-Mutation. Broad reuse: Außenwand, Innenwand, Dach,
 * Boden — nur die Übergangswiderstände (Rsi/Rse) unterscheiden sich je Bauteil/Richtung.
 */

export interface Schicht {
  name?: string;
  /** Dicke, mm. */
  dicke: number;
  /** Wärmeleitfähigkeit λ, W/(m·K). */
  lambda: number;
}

export type BauteilArt = 'aussenwand' | 'innenwand' | 'dach' | 'boden';

/** Übergangswiderstände Rsi/Rse (m²K/W) nach DIN EN ISO 6946, Wärmestromrichtung. */
export const UEBERGANG: Record<BauteilArt, { rsi: number; rse: number }> = {
  aussenwand: { rsi: 0.13, rse: 0.04 }, // horizontal
  innenwand:  { rsi: 0.13, rse: 0.13 }, // beidseitig innen
  dach:       { rsi: 0.10, rse: 0.04 }, // aufwärts
  boden:      { rsi: 0.17, rse: 0.00 }, // abwärts, erdberührt Rse≈0
};

export type PruefSchwere = 'info' | 'warnung' | 'fehler';
export interface UPruefung { id: string; schwere: PruefSchwere; meldung: string; bestanden: boolean; }

export interface UErgebnis {
  gesamtdicke: number;        // mm
  rBauteil: number;           // Σ d/λ (ohne Übergang), m²K/W
  rGesamt: number;            // inkl. Rsi+Rse
  uWert: number;              // W/(m²K)
  schichtR: number[];         // R je Schicht, m²K/W
  pruefungen: UPruefung[];
}

const r3 = (x: number): number => Math.round(x * 1000) / 1000;

export interface UOptionen {
  bauteil?: BauteilArt;        // Default 'aussenwand'
  /** Ziel-U-Wert für die Prüfung, W/(m²K) (Default: GEG-Anhaltswert je Bauteil). */
  zielU?: number;
}

const ZIEL_U: Record<BauteilArt, number> = { aussenwand: 0.24, innenwand: 0.75, dach: 0.20, boden: 0.30 };

export function berechneUWert(schichten: ReadonlyArray<Schicht>, opt?: UOptionen): UErgebnis {
  const bauteil = opt?.bauteil ?? 'aussenwand';
  const { rsi, rse } = UEBERGANG[bauteil];
  const schichtR = schichten.map((s) => (s.lambda > 0 ? s.dicke / 1000 / s.lambda : 0));
  const rBauteil = schichtR.reduce((a, b) => a + b, 0);
  const rGesamt = rsi + rBauteil + rse;
  const uWert = rGesamt > 0 ? 1 / rGesamt : Infinity;
  const gesamtdicke = schichten.reduce((a, s) => a + s.dicke, 0);
  const ziel = opt?.zielU ?? ZIEL_U[bauteil];

  const pruefungen: UPruefung[] = [
    { id: 'ziel-u', schwere: 'warnung', bestanden: uWert <= ziel + 1e-9,
      meldung: `U = ${r3(uWert)} W/(m²K) ${uWert <= ziel ? '≤' : '>'} Ziel ${ziel} (${bauteil}).` },
  ];

  return {
    gesamtdicke,
    rBauteil: r3(rBauteil),
    rGesamt: r3(rGesamt),
    uWert: r3(uWert),
    schichtR: schichtR.map(r3),
    pruefungen,
  };
}
