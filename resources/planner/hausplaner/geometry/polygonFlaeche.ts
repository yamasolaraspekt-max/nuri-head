/**
 * Reparatur 6: reine, testbare Polygon-Flächenberechnung für Dachflächen des
 * 3D-Planers.
 *
 * Problem vorher: Die Material-/Holzliste berechnete die Dachfläche teils als
 * Rechteck-Rahmen (width × height). Für rechteckige Satteldachflächen ist das
 * unauffällig, für Walm-, L-, T- und sonstige polygonale Flächen aber deutlich
 * zu hoch -> überhöhte Flächen-/Materialmengen.
 *
 * Lösung: Die echte Polygonfläche per Gaußscher Flächenformel (Shoelace).
 * Eingabe sind die 2D-Punkte der Dachfläche IN DER (geneigten) Flächenebene,
 * in Metern (so liegt surf.polygon vor: lokale u/v-Koordinaten der Dachfläche).
 * Damit ist das Ergebnis die echte geneigte Dachfläche in m².
 *
 * Diese Datei ist bewusst rein (keine React-/THREE-Abhängigkeit). Sie nimmt
 * beliebige Objekte mit numerischen x/y entgegen (auch THREE.Vector2).
 */

export interface Punkt2D {
  x: number;
  y: number;
}

/**
 * Fläche eines einfachen Polygons in m² (Gaußsche Flächenformel / Shoelace).
 * - < 3 Punkte           -> 0 (keine Fläche)
 * - ungültige Zahlen     -> 0 (kein NaN/Infinity)
 * - umgekehrte Reihenfolge -> identisches (positives) Ergebnis (Betrag)
 * Niemals NaN oder Infinity.
 */
export function polygonFlaecheM2(punkte: ReadonlyArray<Punkt2D> | undefined | null): number {
  if (!Array.isArray(punkte) || punkte.length < 3) return 0;
  let summe = 0;
  for (let i = 0; i < punkte.length; i++) {
    const a = punkte[i];
    const b = punkte[(i + 1) % punkte.length];
    if (
      !a || !b ||
      !Number.isFinite(a.x) || !Number.isFinite(a.y) ||
      !Number.isFinite(b.x) || !Number.isFinite(b.y)
    ) {
      return 0;
    }
    summe += a.x * b.y - b.x * a.y;
  }
  const flaeche = Math.abs(summe) / 2;
  return Number.isFinite(flaeche) ? flaeche : 0;
}
