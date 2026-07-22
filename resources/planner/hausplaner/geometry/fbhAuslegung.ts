/**
 * Hausplaner — Fußbodenheizung-Auslegung (Konfigurator K6, autark nutzbar).
 *
 * Yamas Abschnitt 10: für eine FBH genügt eine beheizbare Fläche mit Bodenaufbau. Reine
 * Auslegungs-Geometrie: nutzbare Heizfläche, Rohrlänge aus Verlegeabstand, Heizkreis-Aufteilung
 * nach maximaler Kreislänge, spezifische Leistung + Vorprüfungen. GRENZE: hydraulischer Abgleich
 * und normative Auslegung bleiben Fach-Engine — hier Rohrlängen/Kreise/Plausibilität.
 */

export interface FbhEingabe {
  /** beheizbare Grundfläche, m². */
  flaeche: number;
  /** Raumheizlast, W. */
  heizlast: number;
  /** Verlegeabstand, mm (Default 150). */
  verlegeabstand?: number;
  /** nicht beheizte Sperrfläche (Möbel/Einbau), m² (Default 0). */
  sperrflaeche?: number;
  /** maximale Heizkreislänge, m (Default 100). */
  maxKreisLaenge?: number;
  /** Zuschlag für Anbindeleitung je Kreis, m (Default 5). */
  anbindungProKreis?: number;
}

export type PruefSchwere = 'info' | 'warnung' | 'fehler';
export interface FbhPruefung { id: string; schwere: PruefSchwere; meldung: string; bestanden: boolean; }

export interface FbhErgebnis {
  nutzflaeche: number;          // m²
  rohrlaengeGesamt: number;     // m (inkl. Anbindung)
  anzahlHeizkreise: number;
  rohrProKreis: number;         // m (längster Kreis, gerundet)
  spezifischeLeistung: number;  // W/m²
  pruefungen: FbhPruefung[];
  bestanden: boolean;
}

const r1 = (x: number): number => Math.round(x * 10) / 10;

export function fbhAuslegung(e: FbhEingabe): FbhErgebnis {
  const va = e.verlegeabstand && e.verlegeabstand > 0 ? e.verlegeabstand : 150;
  const sperr = e.sperrflaeche ?? 0;
  const maxKreis = e.maxKreisLaenge && e.maxKreisLaenge > 0 ? e.maxKreisLaenge : 100;
  const anbindung = e.anbindungProKreis ?? 5;

  const nutzflaeche = Math.max(0, e.flaeche - sperr);
  // Rohrlänge je m² = 1 / Verlegeabstand[m]; VA in mm → *1000.
  const rohrVerlegt = (nutzflaeche * 1000) / va;
  const spez = nutzflaeche > 0 ? e.heizlast / nutzflaeche : 0;

  // Heizkreise so aufteilen, dass jeder Kreis (Verlegung/Kreis + Anbindung) ≤ maxKreis.
  const nutzbarProKreis = Math.max(1, maxKreis - anbindung);
  const anzahlHeizkreise = Math.max(1, Math.ceil(rohrVerlegt / nutzbarProKreis));
  const rohrProKreisVerlegt = rohrVerlegt / anzahlHeizkreise;
  const rohrProKreis = rohrProKreisVerlegt + anbindung;
  const rohrlaengeGesamt = rohrVerlegt + anzahlHeizkreise * anbindung;

  const p: FbhPruefung[] = [];
  p.push({ id: 'spez-leistung', schwere: 'warnung', bestanden: spez <= 100,
    meldung: `Spezifische Leistung ${r1(spez)} W/m² ${spez <= 100 ? '≤' : '>'} 100 W/m² (Komfort-/Oberflächengrenze).` });
  p.push({ id: 'kreislaenge', schwere: 'fehler', bestanden: rohrProKreis <= maxKreis + 0.5,
    meldung: `Längster Kreis ${r1(rohrProKreis)} m ${rohrProKreis <= maxKreis + 0.5 ? '≤' : '>'} max ${maxKreis} m.` });
  p.push({ id: 'nutzflaeche', schwere: 'fehler', bestanden: nutzflaeche > 0,
    meldung: nutzflaeche > 0 ? `Nutzfläche ${r1(nutzflaeche)} m².` : 'Keine beheizbare Nutzfläche (Sperrfläche ≥ Fläche).' });

  return {
    nutzflaeche: r1(nutzflaeche),
    rohrlaengeGesamt: r1(rohrlaengeGesamt),
    anzahlHeizkreise,
    rohrProKreis: r1(rohrProKreis),
    spezifischeLeistung: r1(spez),
    pruefungen: p,
    bestanden: !p.some((x) => x.schwere === 'fehler' && !x.bestanden),
  };
}
