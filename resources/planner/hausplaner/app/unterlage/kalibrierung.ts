/**
 * AUF-88-P1 / K-04 — der Maßstab aus einer bekannten Strecke, als reine Funktion.
 *
 * **Zwei Punkte auf der Unterlage, in Szenen-mm** (demselben Koordinatensystem, in dem die
 * Unterlage gerade gezeichnet wird — mit welchem Maßstab auch immer sie GERADE liegt) **plus eine
 * eingegebene Länge** ergeben den KORRIGIERTEN Maßstab: der alte Maßstab wird um das Verhältnis
 * „eingegeben ÷ gemessen" skaliert.
 *
 * **Warum eine Korrektur und keine Neuberechnung aus Bildpixeln:** die Unterlage liegt in der
 * Szene immer schon mit IRGENDEINEM Maßstab (Standard 1 mm/Einheit vor der ersten Kalibrierung).
 * Die beiden Klickpunkte kommen deshalb bereits in Szenen-mm an — dieselbe Wahrheit, mit der auch
 * Wände gezeichnet werden, keine zweite Pixel-Rechnung daneben. Eine zweite Kalibrierung
 * (Nachbessern) korrigiert genauso: sie geht vom zuletzt gültigen Maßstab aus, nicht von 1.
 */

export interface Punkt {
  x: number;
  y: number;
}

/** Der Standard-Maßstab, bevor kalibriert wurde: 1 mm Bild = 1 mm Szene. Kein Sollwert — ein Startwert. */
export const MASSSTAB_STANDARD = 1;

/** Abstand zweier Punkte — Pythagoras, nichts weiter. */
export function abstand(a: Punkt, b: Punkt): number {
  return Math.hypot(b.x - a.x, b.y - a.y);
}

/**
 * Der korrigierte Maßstab. `null`, wenn die Eingabe nicht auswertbar ist (Punkte identisch, Länge
 * ≤ 0) — **kein NaN, keine Division durch 0**, ein Aufrufer muss nicht selbst darauf prüfen.
 */
export function berechneMassstab(
  alterMassstab: number,
  a: Punkt,
  b: Punkt,
  eingegebeneLaengeMm: number,
): number | null {
  if (!(eingegebeneLaengeMm > 0) || !(alterMassstab > 0)) return null;
  const gemessen = abstand(a, b);
  if (gemessen <= 0) return null;

  return alterMassstab * (eingegebeneLaengeMm / gemessen);
}
