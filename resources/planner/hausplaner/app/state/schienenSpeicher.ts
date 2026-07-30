/**
 * AUF-83-T5 / K-04 — der Klappzustand der beiden Schienen überlebt einen Neuladen, je Arbeitsbereich.
 *
 * **Dieselbe Grenze wie `arbeitsbereichSpeicher.ts`:** der Klappzustand ist eine **Einstellung des
 * Bedieners**, keine Eigenschaft des Gebäudes. Er gehört deshalb **nicht** ins Szenendokument —
 * kein Feld, kein Zod, keine Migration an Bestandsdaten (DAUERDIREKTIVE). Zwei Planer am selben
 * Objekt dürfen unterschiedlich klappen.
 *
 * **Gewählter Ort: `localStorage`**, wortgleiche Begründung wie dort: gerätebezogen, ohne Netz
 * benutzbar, verlustfrei. Geht der Eintrag verloren, sind beide Schienen offen — der Standard, der
 * auch heute der einzige Zustand ist. Kein Datenverlust.
 *
 * **Warum EIN Schlüssel für BEIDE Schienen und alle Arbeitsbereiche, nicht viele:** ein Schlüssel
 * je Kombination (Schiene × Arbeitsbereich) würde bei jedem neuen Arbeitsbereich wachsen und ließe
 * sich nicht in einem Zug lesen. Ein Objekt `{ [arbeitsbereich]: { links, rechts } }` unter einem
 * Schlüssel ist die kleinere Fläche.
 *
 * **Weißliste beim Lesen, nicht der rohe Text:** ein fremder oder kaputter Eintrag liefert den
 * STANDARD (beide offen) — anders als `arbeitsbereichSpeicher`, das dafür `undefined` liefert.
 * Der Unterschied ist bewusst: dort entscheidet der Aufrufer den Standard (`Architektur`, das
 * einzige, das es vorher gab); hier gibt es für „welche Schiene ist offen" nur einen sinnvollen
 * Standard, und ihn im Modul zu tragen erspart jedem Aufrufer, ihn zu kennen.
 *
 * **Kein Absturz ohne Browser:** im Testlauf und beim serverseitigen Rendern gibt es kein
 * `localStorage`; dann liefert `ladeSchienen` den Standard und `speichereSchienen` schreibt nichts.
 */

const SCHLUESSEL = 'hausplaner.schienen.v1';

/** Welche der beiden Schienen. */
export type SchienenSeite = 'links' | 'rechts';

/** Der Klappzustand beider Schienen — `true` heißt offen, wie beide es heute immer sind. */
export interface SchienenZustand {
  links: boolean;
  rechts: boolean;
}

/** Der Standard: beide offen — das heutige, einzige Verhalten. */
export const SCHIENEN_STANDARD: SchienenZustand = { links: true, rechts: true };

function istZustand(x: unknown): x is SchienenZustand {
  if (typeof x !== 'object' || x === null) return false;
  const o = x as Record<string, unknown>;
  return typeof o.links === 'boolean' && typeof o.rechts === 'boolean';
}

/**
 * Der Klappzustand für einen Arbeitsbereich. **Unbekannt, kaputt oder fehlend ⇒ Standard** — nie
 * der rohe Wert, nie ein Wurf.
 */
export function ladeSchienen(arbeitsbereich: string): SchienenZustand {
  try {
    if (typeof localStorage === 'undefined') return SCHIENEN_STANDARD;
    const roh = localStorage.getItem(SCHLUESSEL);
    if (!roh) return SCHIENEN_STANDARD;
    const alle: unknown = JSON.parse(roh);
    if (typeof alle !== 'object' || alle === null) return SCHIENEN_STANDARD;
    const eintrag = (alle as Record<string, unknown>)[arbeitsbereich];
    return istZustand(eintrag) ? eintrag : SCHIENEN_STANDARD;
  } catch {
    return SCHIENEN_STANDARD;
  }
}

/**
 * Schreibt den Klappzustand für EINEN Arbeitsbereich, ohne die anderen zu berühren — dieselbe
 * Read-Modify-Write-Vorsicht wie bei `angeheftet.ts`.
 */
export function speichereSchienen(arbeitsbereich: string, zustand: SchienenZustand): void {
  try {
    if (typeof localStorage === 'undefined') return;
    let alle: Record<string, unknown> = {};
    const roh = localStorage.getItem(SCHLUESSEL);
    if (roh) {
      try {
        const geparst: unknown = JSON.parse(roh);
        if (typeof geparst === 'object' && geparst !== null) alle = geparst as Record<string, unknown>;
      } catch {
        // Kaputter Altbestand — wird durch den neuen Eintrag ersetzt, nicht vermehrt.
      }
    }
    alle[arbeitsbereich] = zustand;
    localStorage.setItem(SCHLUESSEL, JSON.stringify(alle));
  } catch {
    // Voller oder gesperrter Speicher (privater Modus): die Schienen funktionieren weiter,
    // sie merken sich den Zustand nur nicht.
  }
}

/** Der Speicherschlüssel — für Tests und für den späteren Umzug in ein Nutzer-Setting. */
export const SCHIENEN_SCHLUESSEL = SCHLUESSEL;
