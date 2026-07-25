/**
 * Dashboard v2.2 (§20 / UI-5) — die Reiter des Eigenschaftenpanels als DATEN.
 *
 * Warum ein eigenes Modul und nicht Markup im Panel: Anzahl, Reihenfolge und Zustand der Reiter
 * sind damit prüfbar (siehe `__tests__/panelTabs.test.ts`) statt in JSX verstreut. Reine Daten —
 * kein React zur Laufzeit; `StudioZustand` wird als `import type` bezogen und beim Übersetzen
 * entfernt.
 *
 * Yamas Regel „erst Layout, Funktion darf fehlen": drei der vier Reiter sind heute Fläche. Eine
 * leere Fläche ist nur zulässig, wenn sie ihren Zustand ausspricht — deshalb trägt jeder Reiter
 * seinen `zustand` (Badge im Panel, Text UND Symbol) und einen `hinweis`, der sagt, was dort
 * einmal stehen wird. Kein Blindtext, kein „keine Daten".
 *
 * `pruefungen` wird in **Batch 2** (v2.4 Prüfungscenter) auf `verfuegbar` gehoben — hier bewusst
 * noch `in_entwicklung`, weil die Fläche in Batch 1 noch nichts anzeigt.
 */
import type { StudioZustand } from '../studioUi';

export type PanelTabId = 'allgemein' | 'beziehungen' | 'pruefungen' | 'historie';

export interface PanelTab {
  id: PanelTabId;
  label: string;
  zustand: StudioZustand;
  /** Ein Satz: was dieser Reiter zeigen wird. Steht sichtbar in der leeren Fläche. */
  hinweis: string;
}

/** Anzeigereihenfolge im Panel. Feste Reihenfolge = Abnahmekriterium (Batch 1, Punkt 3). */
export const PANEL_TABS: readonly PanelTab[] = [
  {
    id: 'allgemein',
    label: 'Allgemein',
    zustand: 'verfuegbar',
    hinweis: 'Maße, Typ und Parameter des ausgewählten Bauteils.',
  },
  {
    id: 'beziehungen',
    label: 'Beziehungen',
    zustand: 'in_entwicklung',
    hinweis: 'Zeigt später, woran ein Bauteil hängt: Wand ↔ Öffnung, Geschoss, Dachfläche.',
  },
  {
    id: 'pruefungen',
    label: 'Prüfungen',
    zustand: 'in_entwicklung',
    hinweis: 'Zeigt später offene Befunde zum Bauteil (Prüfungscenter, v2.4).',
  },
  {
    id: 'historie',
    label: 'Historie',
    zustand: 'in_entwicklung',
    hinweis: 'Zeigt später, welche Befehle dieses Bauteil verändert haben.',
  },
];

/** Ein Reiter nach id (oder undefined). */
export function panelTab(id: string): PanelTab | undefined {
  return PANEL_TABS.find((t) => t.id === id);
}
