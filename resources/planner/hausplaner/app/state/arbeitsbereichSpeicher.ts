/**
 * AUF-34 / Kante 4 — der gewählte Arbeitsbereich überlebt einen Neuladen.
 *
 * **Die Grenze zuerst:** Der Arbeitsbereich ist eine **Einstellung des Bedieners**, keine
 * Eigenschaft des Gebäudes. Er gehört deshalb **nicht** ins Szenendokument — kein Feld, kein Zod,
 * kein Schema, keine Migration an Bestandsdaten (DAUERDIREKTIVE). Zwei Planer am selben Objekt
 * dürfen in verschiedenen Bereichen arbeiten.
 *
 * **Gewählter Ort: `localStorage`**, vom Auftrag zugelassen — dieselbe Entscheidung und dieselbe
 * Begründung wie bei den angehefteten Werkzeugen (`state/angeheftet.ts`): gerätebezogen, ohne Netz
 * benutzbar, verlustfrei. Geht der Eintrag verloren, startet man in `Architektur` — dem Standard,
 * der auch vorher der einzige Wert war. Kein Datenverlust.
 *
 * **Kein Absturz ohne Browser:** im Testlauf und beim serverseitigen Rendern gibt es kein
 * `localStorage`; dann liefert das Modul `undefined` und schreibt nichts, statt zu werfen.
 */
import { ARBEITSBEREICHE } from '../dashboard/arbeitsbereiche';

const SCHLUESSEL = 'hausplaner.arbeitsbereich.v1';

/**
 * Liest den gespeicherten Bereich. **Unbekannter Wert ⇒ `undefined`**, nicht der rohe Text: ein
 * alter Eintrag aus einer früheren Fassung würde die Leiste sonst auf einen Bereich stellen, den es
 * nicht mehr gibt — sichtbar wäre dann nur noch das Durchgängige.
 */
export function ladeArbeitsbereich(): string | undefined {
  try {
    if (typeof localStorage === 'undefined') return undefined;
    const roh = localStorage.getItem(SCHLUESSEL);
    if (!roh) return undefined;
    return ARBEITSBEREICHE.some((b) => b.id === roh) ? roh : undefined;
  } catch {
    return undefined;
  }
}

/** Schreibt den gewählten Bereich. Ohne Browser ein no-op. */
export function speichereArbeitsbereich(id: string): void {
  try {
    if (typeof localStorage === 'undefined') return;
    localStorage.setItem(SCHLUESSEL, id);
  } catch {
    // Voller oder gesperrter Speicher (privater Modus): die Leiste funktioniert weiter,
    // sie merkt sich den Bereich nur nicht.
  }
}

/** Der Speicherschlüssel — für Tests und für den späteren Umzug in ein Nutzer-Setting. */
export const ARBEITSBEREICH_SCHLUESSEL = SCHLUESSEL;
