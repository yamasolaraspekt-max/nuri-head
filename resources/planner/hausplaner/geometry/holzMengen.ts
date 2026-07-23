/**
 * Reparatur 7: reine, testbare Aggregation der Holzlängen aus der ECHTEN, bereits
 * in der 3D-Geometrie erzeugten Holzliste des Planers.
 *
 * Problem vorher: Die Material-/Holzliste schätzte Sparren-/Lattenlängen aus dem
 * Rechteck-Rahmen (Anzahl × Höhe bzw. Anzahl × Breite). Die Engine zeichnet die
 * Stäbe aber bereits an die reale (an Walm/L/T geclippte) Geometrie -> zwei
 * Wahrheiten. Diese Funktion summiert die echten Stab-Längen je Bauteilart, damit
 * 3D-Darstellung und Holzliste dieselbe Mengenbasis nutzen.
 *
 * Quelle (RoofEngine.holzliste, je Stück): { type: 'sparren'|'latte', name, laenge, ... }
 *  - Sparren:      type 'sparren', name beginnt mit 'Sparren'
 *  - Schiftsparren: type 'sparren', name beginnt mit 'Schiftsparren' (EA28: an Kehle/Grat
 *                  geclippter Gemeinsparren — ZÄHLT als Sparren, sonst Mengen-Unter-Count!)
 *  - Konterlatte:  type 'sparren', name 'Konterlatte'   (läuft auf dem Sparren)
 *  - Traglatte:    type 'latte',   name 'Traglatte'
 *
 * Robust: ungültige/negative Längen werden zu 0; niemals NaN/Infinity/negativ.
 * Rein (keine React-/THREE-Abhängigkeit), vollständig prüfbar. Ändert NICHTS an
 * Reparatur 1-6 (insb. die Polygonfläche aus Reparatur 6 bleibt die Flächenquelle).
 */

export interface HolzStueck {
  type?: string;
  name?: string;
  laenge?: number;
}

export interface HolzMengen {
  /** Summe der echten Sparrenlängen (lfm), ohne Konterlatten. */
  sparrenLaenge: number;
  /** Summe der echten Konterlattenlängen (lfm). */
  konterLaenge: number;
  /** Summe der echten Traglattenlängen (lfm). */
  lattenLaenge: number;
  /** Anzahl echter Sparren (Stück). */
  sparrenAnzahl: number;
}

function gueltigeLaenge(l: unknown): number {
  return typeof l === "number" && Number.isFinite(l) && l > 0 ? l : 0;
}

export function holzMengenAusListe(holzliste: ReadonlyArray<HolzStueck> | undefined | null): HolzMengen {
  let sparrenLaenge = 0;
  let konterLaenge = 0;
  let lattenLaenge = 0;
  let sparrenAnzahl = 0;
  for (const stk of Array.isArray(holzliste) ? holzliste : []) {
    if (!stk) continue;
    const L = gueltigeLaenge(stk.laenge);
    if (stk.type === "latte") {
      lattenLaenge += L;
    } else if (stk.name === "Konterlatte") {
      konterLaenge += L;
    } else if (typeof stk.name === "string" && (stk.name.startsWith("Sparren") || stk.name.startsWith("Schiftsparren"))) {
      // EA28: Schiftsparren sind Gemeinsparren (nur verkürzt/angeschnitten) — sie MÜSSEN hier mitzählen,
      // sonst fallen die an Kehle/Grat geclippten Sparren aus Bauholz-m³ und Lohn heraus (Unter-Count).
      sparrenLaenge += L;
      if (L > 0) sparrenAnzahl += 1;
    }
  }
  return { sparrenLaenge, konterLaenge, lattenLaenge, sparrenAnzahl };
}
