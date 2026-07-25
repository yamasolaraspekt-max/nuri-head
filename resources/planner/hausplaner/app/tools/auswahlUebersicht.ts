/**
 * AUF-35a / Kante 4 — die **Mehrfach-Ansicht** des Panels als reine Funktion.
 *
 * **Die Kante wörtlich:** *„Mehrfachauswahl gemischter Typen (Wand + Fenster + Dach): das Panel
 * darf nicht raten. Es zeigt eine Mehrfach-Ansicht mit Anzahl je Typ, keine Einzelfelder."*
 *
 * Warum das eine eigene Funktion ist und nicht im Panel-JSX entsteht: „zwei Wände, ein Fenster"
 * ist eine **Zählung**, und eine Zählung gehört geprüft, nicht angesehen. Dieselbe Entscheidung
 * wie bei `werkzeugGruppen` und `projektBaum` — Daten rein, Markup dünn.
 *
 * **Deutsche Bezeichnungen, Plural inbegriffen.** Ein Panel, das „3 wall" schreibt, ist kein
 * deutsches Panel. Die Tabelle steht hier, weil sie zur Zählung gehört und nicht ins Markup.
 */
import type { SceneNode } from '../../domain/scene.types';

/** Typ-Bezeichnung Einzahl/Mehrzahl. Nur Anzeige — der gespeicherte `type` bleibt englisch. */
const BEZEICHNUNG: Readonly<Record<string, { eins: string; viele: string }>> = {
  wall: { eins: 'Wand', viele: 'Wände' },
  window: { eins: 'Fenster', viele: 'Fenster' },
  door: { eins: 'Tür', viele: 'Türen' },
  opening: { eins: 'Öffnung', viele: 'Öffnungen' },
  roof: { eins: 'Dach', viele: 'Dächer' },
  ceiling: { eins: 'Decke', viele: 'Decken' },
  zone: { eins: 'Zone', viele: 'Zonen' },
  route: { eins: 'Leitung', viele: 'Leitungen' },
  object: { eins: 'Objekt', viele: 'Objekte' },
};

export interface TypZaehlung {
  /** Der gespeicherte Typ — unverändert, damit er zum Schema passt. */
  typ: string;
  anzahl: number;
  /** Deutsche Beschriftung inklusive Plural: „3 Wände", „1 Dach". */
  bezeichnung: string;
}

export interface MehrfachUebersicht {
  gesamt: number;
  /** Zählung je Typ, absteigend nach Anzahl; bei Gleichstand alphabetisch — stabil, nicht zufällig. */
  typen: TypZaehlung[];
  /** Wieviele der ausgewählten Objekte sind gesperrt? Sie bleiben wählbar (Kante 1). */
  gesperrt: number;
}

/**
 * Zählt eine Auswahl. Unbekannte ids werden übergangen (eine Auswahl kann einem gelöschten Objekt
 * folgen) — sie werfen nicht und erscheinen nicht als „0 undefined".
 */
export function mehrfachUebersicht(ids: readonly string[], nodes: readonly SceneNode[]): MehrfachUebersicht {
  const gewaehlt = ids
    .map((id) => nodes.find((n) => n.id === id))
    .filter((n): n is SceneNode => Boolean(n));

  const zaehler = new Map<string, number>();
  for (const n of gewaehlt) zaehler.set(n.type, (zaehler.get(n.type) ?? 0) + 1);

  const typen = [...zaehler]
    .map(([typ, anzahl]) => ({ typ, anzahl, bezeichnung: `${anzahl} ${benenne(typ, anzahl)}` }))
    .sort((a, b) => b.anzahl - a.anzahl || a.typ.localeCompare(b.typ));

  return {
    gesamt: gewaehlt.length,
    typen,
    gesperrt: gewaehlt.filter((n) => n.locked).length,
  };
}

/** Deutsche Benennung eines Typs. Unbekannter Typ ⇒ der rohe Typ, statt etwas zu erfinden. */
export function benenne(typ: string, anzahl: number): string {
  const b = BEZEICHNUNG[typ];
  if (!b) return typ;
  return anzahl === 1 ? b.eins : b.viele;
}
