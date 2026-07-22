/**
 * Hausplaner — Treppen-Berechnung (Konfigurator K3, autark nutzbar).
 *
 * Reine, deterministische Auslegung: optimale Stufenzahl, Steigung, Auftritt, Schrittmaß und
 * die Prüfungen nach DIN 18065 (Schrittmaß-, Bequemlichkeits-, Sicherheitsregel; Grenzmaße je
 * Nutzungsbereich). Keine Geometrie-/Szene-Mutation — nur Zahlen + Prüfungen. Für eine Treppe
 * braucht man nicht das ganze Gebäude (Yamas Autark-Prinzip): Eingabe sind Höhen + Fläche.
 */

export type TreppenBereich = 'wohnung' | 'gebaeude' | 'aussen';

export interface TreppenEingabe {
  /** Geschosshöhe OKFF unten bis OKFF oben, mm. */
  geschosshoehe: number;
  /** Ziel-Steigungshöhe, mm (Default 175 — bequemer Wohnungsbau). */
  gewuenschteSteigung?: number;
  /** verfügbare Lauflänge (Grundfläche in Laufrichtung), mm. Wenn gesetzt → Auftritt daraus. */
  verfuegbareLauflaenge?: number;
  /** Laufbreite, mm. */
  laufbreite?: number;
  /** lichte Durchgangs-/Kopfhöhe, mm. */
  durchgangshoehe?: number;
  /** Nutzungsbereich bestimmt die Grenzmaße (Default 'wohnung'). */
  bereich?: TreppenBereich;
}

export type PruefSchwere = 'info' | 'warnung' | 'fehler';
export interface TreppenPruefung {
  id: string;
  schwere: PruefSchwere;
  meldung: string;
  bestanden: boolean;
}

export interface TreppenErgebnis {
  anzahlSteigungen: number;
  /** Auftritte = Steigungen − 1 (oberster Tritt = oberes Geschoss). */
  anzahlAuftritte: number;
  steigungshoehe: number; // mm, auf 0,1 gerundet
  auftritt: number;       // mm, auf 0,1 gerundet
  lauflaenge: number;     // mm
  schrittmass: number;    // 2·Steigung + Auftritt
  bequemlichkeit: number; // Auftritt − Steigung
  sicherheit: number;     // Auftritt + Steigung
  pruefungen: TreppenPruefung[];
  bestanden: boolean;     // keine Prüfung mit Schwere 'fehler'
}

interface Grenzwerte {
  steigungMax: number; auftrittMin: number; laufbreiteMin: number;
}
const GRENZEN: Record<TreppenBereich, Grenzwerte> = {
  wohnung:  { steigungMax: 200, auftrittMin: 230, laufbreiteMin: 800 },
  gebaeude: { steigungMax: 190, auftrittMin: 260, laufbreiteMin: 1000 },
  aussen:   { steigungMax: 160, auftrittMin: 300, laufbreiteMin: 1000 },
};

const DURCHGANG_MIN = 2000; // mm, DIN 18065 lichte Höhe
const r1 = (x: number): number => Math.round(x * 10) / 10;

export function berechneTreppe(e: TreppenEingabe): TreppenErgebnis {
  const bereich = e.bereich ?? 'wohnung';
  const g = GRENZEN[bereich];
  const ziel = e.gewuenschteSteigung && e.gewuenschteSteigung > 0 ? e.gewuenschteSteigung : 175;

  const anzahlSteigungen = Math.max(1, Math.round(e.geschosshoehe / ziel));
  const anzahlAuftritte = Math.max(1, anzahlSteigungen - 1);
  const steigungExakt = e.geschosshoehe / anzahlSteigungen;

  const auftrittExakt =
    e.verfuegbareLauflaenge && e.verfuegbareLauflaenge > 0
      ? e.verfuegbareLauflaenge / anzahlAuftritte
      : 630 - 2 * steigungExakt; // Schrittmaßregel

  const schrittmass = 2 * steigungExakt + auftrittExakt;
  const bequemlichkeit = auftrittExakt - steigungExakt;
  const sicherheit = auftrittExakt + steigungExakt;

  const p: TreppenPruefung[] = [];
  const push = (id: string, ok: boolean, schwere: PruefSchwere, meldung: string) =>
    p.push({ id, schwere, meldung, bestanden: ok });

  push('steigung-max', steigungExakt <= g.steigungMax + 0.5, 'fehler',
    `Steigung ${r1(steigungExakt)} mm ${steigungExakt <= g.steigungMax + 0.5 ? '≤' : '>'} zulässig ${g.steigungMax} mm (${bereich}).`);
  push('auftritt-min', auftrittExakt >= g.auftrittMin - 0.5, 'fehler',
    `Auftritt ${r1(auftrittExakt)} mm ${auftrittExakt >= g.auftrittMin - 0.5 ? '≥' : '<'} Mindestmaß ${g.auftrittMin} mm (${bereich}).`);
  push('schrittmass', schrittmass >= 590 && schrittmass <= 650, schrittmass >= 570 && schrittmass <= 670 ? 'warnung' : 'fehler',
    `Schrittmaß ${r1(schrittmass)} mm (Soll 590–650, ideal 630).`);
  push('bequemlichkeit', Math.abs(bequemlichkeit - 120) <= 25, 'warnung',
    `Bequemlichkeitsregel (Auftritt−Steigung) = ${r1(bequemlichkeit)} mm (Ziel ~120).`);
  push('sicherheit', Math.abs(sicherheit - 460) <= 30, 'warnung',
    `Sicherheitsregel (Auftritt+Steigung) = ${r1(sicherheit)} mm (Ziel ~460).`);
  if (e.laufbreite !== undefined) {
    push('laufbreite', e.laufbreite >= g.laufbreiteMin, 'fehler',
      `Laufbreite ${e.laufbreite} mm ${e.laufbreite >= g.laufbreiteMin ? '≥' : '<'} Mindestmaß ${g.laufbreiteMin} mm (${bereich}).`);
  }
  if (e.durchgangshoehe !== undefined) {
    push('durchgangshoehe', e.durchgangshoehe >= DURCHGANG_MIN, 'fehler',
      `Durchgangshöhe ${e.durchgangshoehe} mm ${e.durchgangshoehe >= DURCHGANG_MIN ? '≥' : '<'} ${DURCHGANG_MIN} mm.`);
  }

  return {
    anzahlSteigungen,
    anzahlAuftritte,
    steigungshoehe: r1(steigungExakt),
    auftritt: r1(auftrittExakt),
    lauflaenge: Math.round(anzahlAuftritte * auftrittExakt),
    schrittmass: r1(schrittmass),
    bequemlichkeit: r1(bequemlichkeit),
    sicherheit: r1(sicherheit),
    pruefungen: p,
    bestanden: !p.some((x) => x.schwere === 'fehler' && !x.bestanden),
  };
}
