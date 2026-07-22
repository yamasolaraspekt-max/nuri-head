/**
 * Hausplaner — PV-Schnellbelegung (Konfigurator K4/K7, autark „Schnell"-Stufe).
 *
 * Yamas Abschnitt 7: Für PV muss man nicht das ganze Haus modellieren. Diese reine Geometrie-
 * Funktion belegt eine rechteckige Dachfläche mit Modulen (beide Orientierungen, wählt die
 * bessere) und liefert Modulzahl + kWp + Flächennutzung. GRENZE: Ertrag/Verschattung/Strings
 * bleiben der Fach-Engine (wberechnung) vorbehalten — hier nur Geometrie/Anzahl/Leistung.
 */

export interface PvEingabe {
  /** Dachbreite horizontal, mm. */
  dachLaenge: number;
  /** Dachlänge in Falllinie (entlang der Neigung), mm. */
  dachBreite: number;
  /** Modul-Hochformat: Breite × Höhe, mm. */
  modulBreite: number;
  modulHoehe: number;
  /** Modul-Nennleistung, Wp. */
  modulLeistung: number;
  /** umlaufender Randabstand (First/Traufe/Ortgang), mm (Default 300). */
  randabstand?: number;
  /** Spalt zwischen Modulen, mm (Default 20). */
  modulabstand?: number;
}

export interface PvBelegung {
  orientierung: 'hochkant' | 'quer';
  spalten: number;
  reihen: number;
  moduleGesamt: number;
  kWp: number;
  dachFlaecheM2: number;
  belegteFlaecheM2: number;
  flaechennutzung: number; // %
}

function belegung(nutzL: number, nutzB: number, mW: number, mH: number, gap: number): { spalten: number; reihen: number } {
  const spalten = Math.max(0, Math.floor((nutzL + gap) / (mW + gap)));
  const reihen = Math.max(0, Math.floor((nutzB + gap) / (mH + gap)));
  return { spalten, reihen };
}

const r2 = (x: number): number => Math.round(x * 100) / 100;

/** Belegt die Fläche in beiden Modul-Orientierungen und liefert die mit mehr Modulen. */
export function pvSchnellBelegung(e: PvEingabe): PvBelegung {
  const rand = e.randabstand ?? 300;
  const gap = e.modulabstand ?? 20;
  const nutzL = Math.max(0, e.dachLaenge - 2 * rand);
  const nutzB = Math.max(0, e.dachBreite - 2 * rand);

  const hoch = belegung(nutzL, nutzB, e.modulBreite, e.modulHoehe, gap);
  const quer = belegung(nutzL, nutzB, e.modulHoehe, e.modulBreite, gap);

  const hochN = hoch.spalten * hoch.reihen;
  const querN = quer.spalten * quer.reihen;
  const nimmHoch = hochN >= querN;
  const b = nimmHoch ? hoch : quer;
  const n = nimmHoch ? hochN : querN;

  const modulFlaeche = (e.modulBreite / 1000) * (e.modulHoehe / 1000); // m²
  const dachFlaeche = (e.dachLaenge / 1000) * (e.dachBreite / 1000);
  const belegt = n * modulFlaeche;

  return {
    orientierung: nimmHoch ? 'hochkant' : 'quer',
    spalten: b.spalten,
    reihen: b.reihen,
    moduleGesamt: n,
    kWp: r2((n * e.modulLeistung) / 1000),
    dachFlaecheM2: r2(dachFlaeche),
    belegteFlaecheM2: r2(belegt),
    flaechennutzung: dachFlaeche > 0 ? r2((belegt / dachFlaeche) * 100) : 0,
  };
}
