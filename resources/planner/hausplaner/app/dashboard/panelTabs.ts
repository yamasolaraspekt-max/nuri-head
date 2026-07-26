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
 * `pruefungen` ist seit **Batch 2** (v2.4 Prüfungscenter) `verfuegbar` — die Fläche zeigt jetzt
 * echte Befunde aus `befunde.ts`. `beziehungen` und `historie` bleiben Fläche.
 *
 * **AUF-55 (26.07.):** `historie` ist der Ort, an dem das Studio einen Verlauf andeutet — und der
 * einzige. Deshalb wird die **Snapshot-Naht** dort ausgesprochen statt in einer neuen Fläche.
 * *Gemessen, bevor gebaut wurde:* im Studio gibt es **keine wirkungslose Snapshot-Fläche**, die man
 * hätte kennzeichnen können; es gibt gar keine. Was es gibt, ist eine Naht in `objekt.blade.php`
 * und drei arbeitende Routen, von denen die Insel nichts weiß.
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
    // Batch 2 (v2.4): der Reiter zeigt jetzt echte Befunde aus `befunde.ts` — deshalb 'verfuegbar'.
    zustand: 'verfuegbar',
    hinweis: 'Offene Befunde zum Plan — heute die zuletzt abgelehnte Aktion (Prüfungscenter, v2.4).',
  },
  {
    id: 'historie',
    label: 'Historie',
    zustand: 'in_entwicklung',
    // AUF-55 — **die Snapshot-Naht wird hier ausgesprochen.** Der Satz nannte bisher nur die
    // Befehlshistorie eines Bauteils. Gemessen ist aber mehr da: `objekt.blade.php` setzt
    // `data-snapshots-url`, drei Routen legen Planungsstände an, listen und stellen sie wieder her
    // — und **niemand in der Insel liest davon auch nur ein Zeichen**. Eine Naht, die niemand
    // sieht, ist schlimmer als eine leere Fläche: sie wird beim nächsten Mal neu erfunden.
    // **Was fehlt, ist die Fläche, nicht der Server** — genau das sagt der Satz jetzt.
    hinweis: 'Zeigt später beides: welche Befehle dieses Bauteil verändert haben — und die '
      + 'gespeicherten Planungsstände des Objekts, die der Server heute schon anlegt, listet und '
      + 'wiederherstellt. Angebunden ist die Fläche noch nicht.',
  },
];

/** Ein Reiter nach id (oder undefined). */
export function panelTab(id: string): PanelTab | undefined {
  return PANEL_TABS.find((t) => t.id === id);
}
