/**
 * I4 (AUF-21) — die persönlich angehefteten Werkzeuge (★).
 *
 * **Die Grenze zuerst, weil sie die wichtigste Entscheidung dieses Moduls ist:** Anheftungen sind
 * eine **Vorliebe des Bedieners**, keine Eigenschaft des Gebäudes. Sie gehören deshalb **nicht** ins
 * Szenendokument — kein neues Feld, kein Zod, kein Schema, keine Migration an Bestandsdaten
 * (DAUERDIREKTIVE). Zwei Planer am selben Objekt dürfen unterschiedliche Sterne haben, und ein
 * Stern darf niemals eine gespeicherte Szene verändern.
 *
 * **Gewählter Ort: `localStorage`**, vom Auftrag ausdrücklich zugelassen. Begründung gegenüber
 * einem Nutzer-Setting in der Datenbank: eine Werkzeugleiste ist gerätebezogen (Büro-Monitor vs.
 * Baustellen-Tablet), sie muss ohne Netz funktionieren, und sie ist verlustfrei — geht der Eintrag
 * verloren, fehlt ein Stern, keine Arbeit. Ein Nutzer-Setting bliebe später **additiv** möglich;
 * dieses Modul ist die einzige Stelle, die das wüsste.
 *
 * **Kein Absturz ohne Browser:** im Testlauf und beim serverseitigen Rendern gibt es kein
 * `localStorage`. Alle Funktionen arbeiten dann auf einer leeren Menge, statt zu werfen.
 */

const SCHLUESSEL = 'hausplaner.angeheftet.v1';

/** Liest die angehefteten ids. Defekter oder fremder Inhalt ⇒ leere Menge, kein Wurf. */
export function ladeAngeheftet(): Set<string> {
  try {
    if (typeof localStorage === 'undefined') return new Set();
    const roh = localStorage.getItem(SCHLUESSEL);
    if (!roh) return new Set();
    const daten: unknown = JSON.parse(roh);
    if (!Array.isArray(daten)) return new Set();
    return new Set(daten.filter((x): x is string => typeof x === 'string'));
  } catch {
    // Kaputter Eintrag darf die Anwendung nicht anhalten — ein verlorener Stern ist kein Datenverlust.
    return new Set();
  }
}

/** Schreibt die angehefteten ids. Ohne Browser ein no-op. */
export function speichereAngeheftet(ids: ReadonlySet<string>): void {
  try {
    if (typeof localStorage === 'undefined') return;
    localStorage.setItem(SCHLUESSEL, JSON.stringify([...ids].sort()));
  } catch {
    // Voller oder gesperrter Speicher (privater Modus): die Leiste funktioniert weiter,
    // sie merkt sich den Stern nur nicht.
  }
}

/**
 * Anheften/Lösen — **rein**: nimmt eine Menge, gibt eine neue zurück, schreibt nichts.
 * So ist der Übergang testbar, ohne einen Browser zu erfinden.
 */
export function umschalten(ids: ReadonlySet<string>, toolId: string): Set<string> {
  const neu = new Set(ids);
  if (neu.has(toolId)) neu.delete(toolId);
  else neu.add(toolId);
  return neu;
}

/** Der Speicherschlüssel — für Tests und für den späteren Umzug in ein Nutzer-Setting. */
export const ANGEHEFTET_SCHLUESSEL = SCHLUESSEL;
