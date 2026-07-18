/**
 * Zentrale, getestete Klemm- und Umrechnungslogik für Dachstuhl-Maße des
 * 3D-Dach-&-PV-Planers.
 *
 * Grundregeln (siehe Reparaturmaßnahme 1 „still falsche Ausgaben stoppen"):
 *  - Eingaben kommen in Zentimeter, die interne Berechnung erfolgt in Meter.
 *  - Es gilt EXAKT derselbe Mindestwert (Floor) wie in der Geometrie-Engine
 *    (RoofEngine.buildRoofFace). So kann es nie passieren, dass die 3D-Geometrie
 *    geklemmt ist, Holz-/Material-/Stückliste aber mit 0/NaN/Infinity weiterrechnen.
 *  - Ungültige Werte (leer/NaN/0/negativ) führen niemals zu ungültigen Ergebnissen,
 *    sondern werden auf den Mindestwert geklemmt.
 *
 * Diese Datei ist bewusst rein (keine React-/THREE-Abhängigkeit) und damit
 * vollständig per Unit-Test prüfbar.
 */

/** Mindestwerte in METERN — identisch zu RoofEngine.buildRoofFace (Z. ~745-748). */
export const DACH_FLOOR_M = {
  rafterDist: 0.05, // Sparrenabstand min 5 cm (Schutz gegen Division durch ~0 / Endlosschleife)
  battenDist: 0.05, // Lattenabstand   min 5 cm
  rafterW: 0.02,    // Sparrenbreite   min 2 cm
  rafterH: 0.02,    // Sparrenhöhe     min 2 cm
} as const;

/** Mindestwerte in ZENTIMETERN (für UI-Hinweise / Anzeige). */
export const DACH_FLOOR_CM = {
  rafterSpacing: DACH_FLOOR_M.rafterDist * 100, // 5 cm
  battenDist: DACH_FLOOR_M.battenDist * 100,    // 5 cm
  rafterWidth: DACH_FLOOR_M.rafterW * 100,      // 2 cm
  rafterHeight: DACH_FLOOR_M.rafterH * 100,     // 2 cm
} as const;

/** true, wenn der Wert kein nutzbarer positiver Zahlenwert ist (leer/NaN/0/negativ). */
export function istUngueltig(v: unknown): boolean {
  return typeof v !== "number" || !Number.isFinite(v) || v <= 0;
}

/**
 * Zentimeter → Meter mit Mindest-Floor. Identisch zur Engine-Formel
 * `Math.max(floor, (v || 0) / 100)`: leer/NaN/0/negativ ergeben den Floor.
 */
export function cmZuMFloor(cm: number, floorM: number): number {
  const sicher = typeof cm === "number" && Number.isFinite(cm) ? cm : 0;
  return Math.max(floorM, sicher / 100);
}

export interface DachstuhlMasseM {
  rafterDist: number;
  battenDist: number;
  rafterW: number;
  rafterH: number;
}

export interface DachstuhlEingabeCm {
  rafterSpacing: number;
  battenDist: number;
  rafterWidth: number;
  rafterHeight: number;
}

/**
 * Die geklemmten Dachstuhl-Maße in Metern — die EINE Wahrheit für Geometrie
 * und Stück-/Holzliste. Verhindert Division durch null / NaN / Infinity.
 */
export function dachstuhlMasseM(b: DachstuhlEingabeCm): DachstuhlMasseM {
  return {
    rafterDist: cmZuMFloor(b.rafterSpacing, DACH_FLOOR_M.rafterDist),
    battenDist: cmZuMFloor(b.battenDist, DACH_FLOOR_M.battenDist),
    rafterW: cmZuMFloor(b.rafterWidth, DACH_FLOOR_M.rafterW),
    rafterH: cmZuMFloor(b.rafterHeight, DACH_FLOOR_M.rafterH),
  };
}

/** Der tatsächlich verwendete Wert in cm (für eine konsistente Anzeige). */
export function effektivCm(cm: number, floorCm: number): number {
  if (typeof cm === "number" && Number.isFinite(cm) && cm >= floorCm) return cm;
  return floorCm;
}

/**
 * Verständliche Hinweise, wenn ein Dachstuhl-Wert leer/0/negativ/zu klein ist
 * und deshalb auf den Mindestwert gesetzt wurde. Leeres Array = alles plausibel.
 */
export function dachstuhlHinweise(b: DachstuhlEingabeCm): string[] {
  const hinweise: string[] = [];
  const pruefe = (wert: number, floorCm: number, name: string) => {
    if (istUngueltig(wert) || wert < floorCm) {
      hinweise.push(`${name} wurde auf den gültigen Mindestwert (${floorCm} cm) gesetzt.`);
    }
  };
  pruefe(b.rafterSpacing, DACH_FLOOR_CM.rafterSpacing, "Sparrenabstand");
  pruefe(b.battenDist, DACH_FLOOR_CM.battenDist, "Lattenabstand");
  pruefe(b.rafterWidth, DACH_FLOOR_CM.rafterWidth, "Sparrenbreite");
  pruefe(b.rafterHeight, DACH_FLOOR_CM.rafterHeight, "Sparrenhöhe");
  return hinweise;
}

/** Sicherer Cosinus-Divisor: verhindert Division durch ~0 bei Neigung nahe 90°. */
export function sichererCos(gradOderRad: number, istGrad = true): number {
  const v = typeof gradOderRad === "number" && Number.isFinite(gradOderRad) ? gradOderRad : 0;
  const rad = istGrad ? (v * Math.PI) / 180 : v;
  return Math.max(1e-3, Math.cos(rad));
}
