/**
 * AUF-27 — die drei Reiter der linken 220-px-Schiene als DATEN.
 *
 * **Gemessen war der Mangel:** die Schiene trug drei Blöcke untereinander in **einer** gemeinsamen
 * Scroll-Höhe — Werkzeuge, Fachplaner und Projektbrowser. Der Projektbrowser war erst nach rund
 * 20 Scroll-Ticks sichtbar. Das verstösst gegen „jede Fläche hat genau einen Hauptjob".
 *
 * **Entscheidung des Planners: Reiter statt Stapel** — immer genau einer sichtbar, jeder mit
 * eigener Scroll-Höhe. Warum Reiter und nicht „Fachplaner raus aus der Schiene": wohin die
 * Fachplaner-Einträge gehören, ist ungeklärt (gemessene Teil-Doppelung 22 Einträge ↔ 19 L4-Flächen,
 * keine 1:1-Deckung). Das ist ein eigener Posten. Bis dahin bleibt alles erreichbar — nur nicht
 * mehr übereinandergestapelt.
 *
 * **Warum ein Datenmodul und nicht Markup:** so ist „genau drei, feste Reihenfolge, Standard ist
 * `werkzeuge`, jede id eindeutig" prüfbar. Reiter, die im JSX entstehen, kann man nur ansehen.
 * Das Rendering übernimmt die gemeinsame `ReiterLeiste` — kein zweiter Tab-Mechanismus.
 */

export type SchienenReiterId = 'werkzeuge' | 'projekt' | 'fachplaner';

export interface SchienenReiter {
  id: SchienenReiterId;
  label: string;
  /** Ein Satz für den Tooltip: was in diesem Reiter steht. */
  hinweis: string;
}

/**
 * Anzeigereihenfolge. Fest — sie folgt der Häufigkeit des Jobs: zeichnen (immer), im Modell etwas
 * finden (oft), in ein Fachgewerk springen (selten).
 */
export const SCHIENEN_REITER: readonly SchienenReiter[] = [
  {
    id: 'werkzeuge',
    label: 'Werkzeuge',
    hinweis: 'Die Pflichtwerkzeuge und alles, was du dir angeheftet hast (★).',
  },
  {
    id: 'projekt',
    label: 'Projekt',
    hinweis: 'Alle Bauteile des Plans, nach Art gruppiert — zum Auffinden und Auswählen.',
  },
  {
    id: 'fachplaner',
    label: 'Fachplaner',
    hinweis: 'Die Fachgewerke und Rechenkerne — je Eintrag steht sein Zustand dabei.',
  },
];

/** Der Reiter, der beim Öffnen sichtbar ist: der häufigste Job. */
export const SCHIENE_STANDARD: SchienenReiterId = 'werkzeuge';

/** Ein Reiter nach id (oder undefined) — dieselbe Form wie `panelTab`, kein zweites Muster. */
export function schienenReiter(id: string): SchienenReiter | undefined {
  return SCHIENEN_REITER.find((r) => r.id === id);
}
