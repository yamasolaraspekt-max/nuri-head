/**
 * Reparatur 8: reine, testbare Aggregation der WEITEREN Holzbauteile (Pfetten,
 * Grat-/Kehlsparren) aus der ECHTEN, bereits in der 3D-Geometrie erzeugten
 * Holzliste des Planers — ergänzend zu Reparatur 7 (holzMengen.ts: Sparren/
 * Konter/Latten). Reparatur 7 bleibt UNVERÄNDERT; dieses Modul liest dieselbe
 * holzliste nur zusätzlich aus.
 *
 * Befund (geometrisch gesichert): RoofEngine zeichnet Pfetten (createBeam, type
 * 'pfette': First-/Fußpfetten), Gratsparren (drawBeamBetweenPoints, type
 * 'gratsparren', echte 3D-Diagonale) und Kehlsparren (type 'kehlsparren') und
 * legt JEDES Stück mit echter Länge in this.holzliste ab. Diese Bauteile sind
 * also keine Schätzung, sondern echte Geometrie — sie fehlen nur als eigene
 * Posten in der Material-/BOM-Liste. Diese Funktion summiert ihre echten Längen.
 *
 * NICHT geometrisch gesichert (daher hier NICHT erzeugt, nur dokumentiert):
 * Mittelpfetten, Schwellen, Wechselhölzer/Auswechslungen an Öffnungen,
 * Schiftersparren als eigene Klasse. Siehe OFFENE_HOLZBAUTEILE.
 *
 * Rein (keine React-/THREE-Abhängigkeit), vollständig prüfbar.
 */

export interface HolzStueckRef {
  type?: string;
  name?: string;
  laenge?: number;
}

export interface HolzBauteilMengen {
  /** Summe echter Pfettenlängen (lfm) — First-/Fußpfetten. */
  pfettenLaenge: number;
  pfettenAnzahl: number;
  /** Summe echter Gratsparrenlängen (lfm, 3D). */
  gratsparrenLaenge: number;
  gratsparrenAnzahl: number;
  /** Summe echter Kehlsparrenlängen (lfm, 3D). */
  kehlsparrenLaenge: number;
  kehlsparrenAnzahl: number;
}

/**
 * Offene Holzbauteil-Klassen, die geometrisch (noch) NICHT zuverlässig vorliegen
 * und deshalb bewusst NICHT als echte Mengen ausgegeben werden (keine erfundenen
 * Werte). Für die ehrliche Dokumentation in UI/Bericht.
 */
export const OFFENE_HOLZBAUTEILE: ReadonlyArray<string> = [
  "Mittelpfette (benötigt Auflagerpunkte/Stuhl — in der Geometrie nicht modelliert)",
  "Schwelle (Auflagerschwelle — nicht modelliert)",
  "Wechselholz / Auswechslung an Kamin/Gaube/Dachfenster (Öffnungsränder + betroffene Sparren nicht eindeutig bestimmt)",
  "Schiftersparren als eigene Klasse (verkürzte Sparren liegen geclippt vor, sind aber nicht eindeutig als Schifter benannt)",
];

function gueltigeLaenge(l: unknown): number {
  return typeof l === "number" && Number.isFinite(l) && l > 0 ? l : 0;
}

export function holzBauteileAusListe(
  holzliste: ReadonlyArray<HolzStueckRef> | undefined | null,
): HolzBauteilMengen {
  let pfettenLaenge = 0, pfettenAnzahl = 0;
  let gratsparrenLaenge = 0, gratsparrenAnzahl = 0;
  let kehlsparrenLaenge = 0, kehlsparrenAnzahl = 0;
  for (const stk of Array.isArray(holzliste) ? holzliste : []) {
    if (!stk) continue;
    const L = gueltigeLaenge(stk.laenge);
    if (L <= 0) continue;
    if (stk.type === "pfette") {
      pfettenLaenge += L;
      pfettenAnzahl += 1;
    } else if (stk.type === "gratsparren") {
      gratsparrenLaenge += L;
      gratsparrenAnzahl += 1;
    } else if (stk.type === "kehlsparren") {
      kehlsparrenLaenge += L;
      kehlsparrenAnzahl += 1;
    }
  }
  return {
    pfettenLaenge, pfettenAnzahl,
    gratsparrenLaenge, gratsparrenAnzahl,
    kehlsparrenLaenge, kehlsparrenAnzahl,
  };
}
