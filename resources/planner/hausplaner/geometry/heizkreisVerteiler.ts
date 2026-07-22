/**
 * Hausplaner — Heizkreis-/Verteiler-Auslegung (Konfigurator §11, autark).
 *
 * Reine Rechenlogik: Massenstrom je Heizkreis aus Leistung und Spreizung, Verteilergröße
 * (Abgänge) und Gesamtdurchfluss. m[kg/h] = Q[W]·3,6 / (c·ΔT), c ≈ 4,18 kJ/(kg·K).
 * GRENZE: hydraulischer Abgleich/Rohrnetz bleibt Fach-Engine — hier Durchfluss + Verteilergröße.
 */

export interface HeizkreisEingabe {
  raum?: string;
  /** Heizlast des Kreises, W. */
  leistung: number;
  vorlauf: number;   // °C
  ruecklauf: number; // °C
}

export interface HeizkreisErgebnis {
  raum?: string;
  spreizung: number;   // K
  durchfluss: number;  // kg/h (≈ l/h)
}

export interface VerteilerErgebnis {
  abgaenge: number;
  gesamtDurchfluss: number; // kg/h
  kreise: HeizkreisErgebnis[];
  pruefungen: { id: string; schwere: 'info' | 'warnung' | 'fehler'; meldung: string; bestanden: boolean }[];
}

const C_WASSER = 4.18; // kJ/(kg·K)
const r1 = (x: number): number => Math.round(x * 10) / 10;

/** Massenstrom eines Kreises, kg/h. */
export function kreisDurchfluss(leistung: number, vorlauf: number, ruecklauf: number): number {
  const dt = vorlauf - ruecklauf;
  if (dt <= 0) return 0;
  return r1((leistung * 3.6) / (C_WASSER * dt));
}

export function auslegeVerteiler(kreise: ReadonlyArray<HeizkreisEingabe>): VerteilerErgebnis {
  const erg: HeizkreisErgebnis[] = kreise.map((k) => ({
    raum: k.raum,
    spreizung: r1(k.vorlauf - k.ruecklauf),
    durchfluss: kreisDurchfluss(k.leistung, k.vorlauf, k.ruecklauf),
  }));
  const gesamt = r1(erg.reduce((a, k) => a + k.durchfluss, 0));

  const pruefungen = [
    { id: 'abgaenge', schwere: 'info' as const, bestanden: kreise.length >= 1,
      meldung: `${kreise.length} Heizkreis${kreise.length === 1 ? '' : 'e'} → Verteiler mit ${kreise.length} Abgängen.` },
    { id: 'spreizung', schwere: 'warnung' as const, bestanden: kreise.every((k) => k.vorlauf - k.ruecklauf > 0),
      meldung: kreise.every((k) => k.vorlauf - k.ruecklauf > 0)
        ? 'Alle Kreise mit positiver Spreizung.'
        : 'Mindestens ein Kreis hat keine gültige Spreizung (Vorlauf ≤ Rücklauf).' },
  ];

  return { abgaenge: kreise.length, gesamtDurchfluss: gesamt, kreise: erg, pruefungen };
}
