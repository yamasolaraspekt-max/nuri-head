/**
 * Hausplaner — Heizkörper-Leistungsumrechnung (Konfigurator K6, autark nutzbar).
 *
 * Yamas Abschnitt 9: für einen Heizkörper genügt Raumheizlast + Anschlussdaten. Reine Physik:
 * Umrechnung der Norm-Leistung (nach EN 442, Normübertemperatur meist 50 K) auf die realen
 * Betriebsbedingungen über den Heizkörper-Exponenten n, plus Über-/Unterdeckungs-Bewertung.
 * GRENZE: normative Raumheizlast bleibt Sache der Fach-Engine — hier nur die Umrechnung.
 */

export interface BetriebsBedingung {
  vorlauf: number;   // °C
  ruecklauf: number; // °C
  raumtemp: number;  // °C
  /** Heizkörper-Exponent (Default 1,3 — typischer Kompaktheizkörper). */
  n?: number;
  /** Norm-Übertemperatur der Nennleistung, K (Default 50 = 75/65/20). */
  normUebertemperatur?: number;
}

/** Arithmetische Übertemperatur Δθ = (Vorlauf+Rücklauf)/2 − Raumtemperatur. */
export function uebertemperatur(b: BetriebsBedingung): number {
  return (b.vorlauf + b.ruecklauf) / 2 - b.raumtemp;
}

const r1 = (x: number): number => Math.round(x * 10) / 10;

/** Norm-Leistung → tatsächliche Leistung bei gegebener Spreizung. */
export function betriebsLeistung(normLeistung: number, b: BetriebsBedingung): number {
  const n = b.n ?? 1.3;
  const dtNorm = b.normUebertemperatur ?? 50;
  const dt = uebertemperatur(b);
  if (dt <= 0) return 0; // kein Wärmeübergang (Vorlauf ≤ Raumtemperatur)
  return r1(normLeistung * Math.pow(dt / dtNorm, n));
}

/** Welche Norm-Leistung ist nötig, um die Raumheizlast unter diesen Bedingungen zu decken? */
export function benoetigteNormleistung(raumheizlast: number, b: BetriebsBedingung): number {
  const n = b.n ?? 1.3;
  const dtNorm = b.normUebertemperatur ?? 50;
  const dt = uebertemperatur(b);
  if (dt <= 0) return Infinity;
  return r1(raumheizlast / Math.pow(dt / dtNorm, n));
}

export interface DeckungErgebnis {
  betriebsLeistung: number; // W
  deckungsgrad: number;     // % der Raumheizlast
  ausreichend: boolean;
  hinweis: string;
}

/** Bewertet, ob ein Heizkörper mit `normLeistung` die Raumheizlast deckt. */
export function bewerteDeckung(normLeistung: number, raumheizlast: number, b: BetriebsBedingung): DeckungErgebnis {
  const q = betriebsLeistung(normLeistung, b);
  const grad = raumheizlast > 0 ? r1((q / raumheizlast) * 100) : 100;
  const ausreichend = q >= raumheizlast;
  return {
    betriebsLeistung: q,
    deckungsgrad: grad,
    ausreichend,
    hinweis: ausreichend
      ? `Deckung ${grad}% — ausreichend (${q} W ≥ ${raumheizlast} W).`
      : `Unterdeckung: nur ${grad}% (${q} W < ${raumheizlast} W). Größeres Modell oder höhere Vorlauftemperatur.`,
  };
}
