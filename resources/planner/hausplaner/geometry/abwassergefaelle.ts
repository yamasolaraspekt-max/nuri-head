/**
 * Hausplaner — Abwassergefälle (Konfigurator Bad/Sanitär §5.6/§9, autark).
 *
 * Reine Prüf-/Rechenlogik nach DIN 1986-100 (vereinfacht): Mindestgefälle je Nennweite,
 * Höhenverlust aus Länge×Gefälle, und die maximal überbrückbare horizontale Distanz bei
 * gegebener verfügbarer Fallhöhe (Fallstrang-Distanz). Keine Szene-Mutation.
 */

export type PruefSchwere = 'info' | 'warnung' | 'fehler';
export interface AbwasserPruefung { id: string; schwere: PruefSchwere; meldung: string; bestanden: boolean; }

/** Praktisches Mindestgefälle in % je DN (Grundleitung ~1:DN, Anschlussleitung etwas mehr). */
export function mindestGefaelle(dn: number): number {
  if (dn <= 50) return 2.0;   // DN40/50 Anschlussleitungen
  if (dn <= 70) return 1.5;   // DN70
  return 1.0;                 // DN100+ Grund-/Sammelleitung (1:100)
}
const MAX_GEFAELLE = 5.0; // % — darüber Selbstverlust/Spülstrom-Probleme

export interface AbwasserEingabe {
  dn: number;
  laenge: number;      // m, horizontal
  gefaelle?: number;   // %, wenn nicht gesetzt → Mindestgefälle
}

export interface AbwasserErgebnis {
  gefaelle: number;        // % (verwendet)
  mindestGefaelle: number; // %
  hoehenverlust: number;   // mm über die Länge
  pruefungen: AbwasserPruefung[];
  bestanden: boolean;
}

const r1 = (x: number): number => Math.round(x * 10) / 10;

export function pruefeAbwasser(e: AbwasserEingabe): AbwasserErgebnis {
  const min = mindestGefaelle(e.dn);
  const gef = e.gefaelle !== undefined ? e.gefaelle : min;
  const hoehenverlust = Math.round(e.laenge * 1000 * (gef / 100));

  const p: AbwasserPruefung[] = [
    { id: 'min-gefaelle', schwere: 'fehler', bestanden: gef >= min - 1e-9,
      meldung: `Gefälle ${r1(gef)} % ${gef >= min ? '≥' : '<'} Mindestgefälle ${min} % (DN${e.dn}).` },
    { id: 'max-gefaelle', schwere: 'warnung', bestanden: gef <= MAX_GEFAELLE + 1e-9,
      meldung: `Gefälle ${r1(gef)} % ${gef <= MAX_GEFAELLE ? '≤' : '>'} empfohlenes Maximum ${MAX_GEFAELLE} %.` },
  ];

  return { gefaelle: r1(gef), mindestGefaelle: min, hoehenverlust, pruefungen: p,
    bestanden: !p.some((x) => x.schwere === 'fehler' && !x.bestanden) };
}

/** Maximale horizontale Distanz (m), die eine verfügbare Fallhöhe beim Mindestgefälle erlaubt. */
export function maxHorizontaleDistanz(dn: number, verfuegbareFallhoeheMm: number): number {
  const min = mindestGefaelle(dn);
  if (min <= 0) return Infinity;
  // Fallhöhe[mm] = laenge[m]*1000*min/100 → laenge = Fallhöhe / (10*min)
  return r1(verfuegbareFallhoeheMm / (10 * min));
}
