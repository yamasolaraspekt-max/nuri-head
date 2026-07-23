/**
 * Hausplaner UI-2 — geteilter UI-State (Master-Prompt §23). GETRENNT vom Modell-Store.
 *
 * Der Modell-Store (`store/hausplanerStore.ts`) bleibt alleinige Wahrheit über das Gebäudemodell
 * (Szene, Auswahl, Speicherstatus, Ansicht `modus`). DIESER Store hält NUR Bedien-Zustand, der
 * heute in einer Komponente (`HausplanerApp`) gefangen war: das AKTIVE WERKZEUG und der aktive
 * ARBEITSBEREICH. Beides ist damit teilbar (Studio-Shell, Kontextleiste, Activation-Engine).
 *
 * Bewusst NICHT hier (Vermeidung zweiter Wahrheiten, s. Planner R3/R4):
 * - Ansicht (2d/split/3d) bleibt im Modell-Store (`modus`) — die Activation-Engine liest sie von
 *   dort. (Rename `modus→viewMode` ist ein eigener Hygiene-Slice.)
 * - Auswahl bleibt im Modell-Store (`selectedNodeIds`).
 */

import { create } from 'zustand';
import { WORKSPACE_ARCHITEKTUR } from '../tools/toolRegistry';
import type { WorkspaceId } from '../tools/toolTypes';

export interface PlannerUiState {
  /** Aktives Werkzeug (id aus der Tool-Registry). Ersetzt das lokale `werkzeug` in HausplanerApp. */
  activeToolId: string;
  /** Aktiver Arbeitsbereich (§9). Heute real nur 'architektur'. */
  activeWorkspace: WorkspaceId;

  setActiveTool: (id: string) => void;
  setActiveWorkspace: (id: WorkspaceId) => void;
  /** Auf den Grundzustand zurücksetzen (Mount/Neuladen). */
  reset: () => void;
}

const DEFAULTS = {
  activeToolId: 'auswahl',
  activeWorkspace: WORKSPACE_ARCHITEKTUR,
} as const;

export const usePlannerUiStore = create<PlannerUiState>((set) => ({
  ...DEFAULTS,
  setActiveTool: (id) => set({ activeToolId: id }),
  setActiveWorkspace: (id) => set({ activeWorkspace: id }),
  reset: () => set({ ...DEFAULTS }),
}));
