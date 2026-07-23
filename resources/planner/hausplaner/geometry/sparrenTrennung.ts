/**
 * Reparatur 10: reine, testbare Logik zur Trennung eines Sparrens an einer Öffnung
 * (Dachfenster/Kamin/...). Ergänzt Reparatur 9 (auswechslung.ts: Sicher-Entscheidung)
 * und lässt diese unverändert.
 *
 * Ein Sparren verläuft in lokalen Dachflächenkoordinaten von v=vStart (Traufe) bis
 * v=vEnd (First). Eine Öffnung belegt den v-Bereich [vMinOpen, vMaxOpen] (inkl.
 * Sicherheitsrand). Liegt die Öffnung VOLL innerhalb des Sparrens und bleiben beide
 * Reststücke >= Mindestmaß, wird der Sparren in ein unteres + ein oberes Teilstück
 * getrennt. Sonst KEINE Trennung (voller Sparren bleibt = Prüffall) — keine zu kurzen
 * oder falschen Teilstücke.
 *
 * Die v-Koordinaten liegen in der geneigten Dachflächenebene (Meter), die Teilstück-
 * Längen sind daher geneigte (echte) Längen, keine Grundrissprojektion.
 *
 * Rein (keine React-/THREE-Abhängigkeit), vollständig prüfbar.
 */

export interface SparrenTeilstueck {
  vStart: number;
  vEnd: number;
  laengeM: number;
  lage: "unten" | "oben";
}

function endlich(v: unknown): boolean {
  return typeof v === "number" && Number.isFinite(v);
}

/**
 * Teilt einen Sparren [vStart, vEnd] an der Öffnung [vMinOpen, vMaxOpen].
 * - Öffnung nicht voll innerhalb (berührt Trauf-/First-Ende) -> [] (nicht sicher trennbar)
 * - eines der Reststücke < minLenM -> [] (zu kurzes Reststück -> prüfpflichtig, voller Sparren bleibt)
 * - sonst: unteres + oberes Teilstück mit echter (geneigter) Länge.
 * Niemals NaN/Infinity/negativ.
 */
export function sparrenTeilstuecke(
  vStart: number,
  vEnd: number,
  vMinOpen: number,
  vMaxOpen: number,
  minLenM = 0.1,
): SparrenTeilstueck[] {
  if (!endlich(vStart) || !endlich(vEnd) || !endlich(vMinOpen) || !endlich(vMaxOpen)) return [];
  if (vEnd <= vStart) return [];
  if (vMaxOpen <= vMinOpen) return [];
  // Öffnung muss VOLL innerhalb des Sparrens liegen (sonst nicht sicher trennbar)
  if (vMinOpen <= vStart || vMaxOpen >= vEnd) return [];
  const untenLen = vMinOpen - vStart;
  const obenLen = vEnd - vMaxOpen;
  if (untenLen < minLenM || obenLen < minLenM) return []; // Reststück zu kurz
  return [
    { vStart, vEnd: vMinOpen, laengeM: untenLen, lage: "unten" },
    { vStart: vMaxOpen, vEnd, laengeM: obenLen, lage: "oben" },
  ];
}

/** true, wenn der Sparren an dieser Öffnung sicher getrennt werden kann. */
export function istSicherTrennbar(
  vStart: number,
  vEnd: number,
  vMinOpen: number,
  vMaxOpen: number,
  minLenM = 0.1,
): boolean {
  return sparrenTeilstuecke(vStart, vEnd, vMinOpen, vMaxOpen, minLenM).length === 2;
}
