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
import type { ProjektEintrag } from './projekte';
import type { Objektkopf } from './objektkopf';
import type { AktuelleUnterlage, UnterlageZustand } from './unterlage';

export interface PlannerUiState {
  /** Aktives Werkzeug (id aus der Tool-Registry). Ersetzt das lokale `werkzeug` in HausplanerApp. */
  activeToolId: string;
  /** Aktiver Arbeitsbereich (§9). Heute real nur 'architektur'. */
  activeWorkspace: WorkspaceId;
  /**
   * AUF-60 — die Rechte des angemeldeten Nutzers, **wie das Blade sie liefert**. Grundzustand ist
   * die leere Liste: das Minimum. Wer nichts gesetzt hat, darf nichts — nie umgekehrt.
   *
   * Hier und nicht im Modell-Store: Rechte gehören zum Bedien-Zustand, nicht ins Gebäudemodell.
   */
  rechte: string[];
  /**
   * AUF-78 — die zuletzt bearbeiteten Projekte des Nutzers, **wie das Blade sie liefert**.
   * Grundzustand ist die leere Liste: der Startbildschirm zeigt dann den ehrlichen Leerzustand
   * aus AUF-40 Teil A, keine Beispielzeile.
   */
  projekte: ProjektEintrag[];
  /**
   * AUF-83-T3 / K-01 — der Objektkopf (Name, Adresse, Übernahme), **wie das Blade ihn liefert**.
   *
   * Grundzustand ist `null`, und das ist keine Notlage: das **Studio hat kein Objekt** (Scratch,
   * kein `data-speichern-url`). Fehlt der Kopf, zeigt die Kopfleiste Projekt und Geschoss — wie
   * bisher. Ein erfundener Objektname wäre genau die Sorte Anzeige, die AUF-40 entfernt hat.
   */
  objektkopf: Objektkopf | null;
  /**
   * AUF-88-P1 — die Referenzunterlage, **wie das Blade sie liefert**.
   *
   * Grundzustand `null`: kein Objekt (Studio) oder das Blade hat keinen Wert geliefert. Anders als
   * bei den Rechten gibt es hier einen zweiten Schreibweg (`aktualisiereUnterlage`) — nach einem
   * Upload ändert sich der Zustand ohne Neuladen der Seite (Polling, Kalibrierung).
   */
  unterlage: UnterlageZustand | null;

  setActiveTool: (id: string) => void;
  setActiveWorkspace: (id: WorkspaceId) => void;
  /** Die gelesenen Rechte hinterlegen. Setzt nur ab, was `leseRechte` liefert — kein Ableiten. */
  setRechte: (rechte: string[]) => void;
  /** Die gelesenen Projekte hinterlegen. Wie bei den Rechten: nur ablegen, nichts ableiten. */
  setProjekte: (projekte: ProjektEintrag[]) => void;
  /** Den gelesenen Objektkopf hinterlegen. Wie Rechte und Projekte: nur ablegen, nichts ableiten. */
  setObjektkopf: (kopf: Objektkopf | null) => void;
  /** Die gelesene Unterlage hinterlegen (beim Mount). */
  setUnterlage: (unterlage: UnterlageZustand | null) => void;
  /**
   * Nur `aktuelle` ersetzen — `objektId`/`hochladenUrl` bleiben, wie sie waren. Für die Antwort
   * des Upload- oder des Status-Aufrufs: der Server liefert wieder dieselbe Form wie beim Mount,
   * die Insel rechnet nichts dazu.
   */
  aktualisiereUnterlage: (aktuelle: AktuelleUnterlage | null) => void;
  /** Auf den Grundzustand zurücksetzen (Mount/Neuladen). */
  reset: () => void;
}

const DEFAULTS = {
  activeToolId: 'auswahl',
  activeWorkspace: WORKSPACE_ARCHITEKTUR,
} as const;

export const usePlannerUiStore = create<PlannerUiState>((set) => ({
  ...DEFAULTS,
  // Grundzustand = Minimum. Absichtlich NICHT in DEFAULTS: `reset()` ist ein Bedien-Reset
  // (Werkzeug/Arbeitsbereich) und läuft beim Mount — es darf die vom Server gelesenen Rechte
  // weder löschen noch wiederherstellen.
  rechte: [],
  projekte: [],
  objektkopf: null,
  unterlage: null,
  setActiveTool: (id) => set({ activeToolId: id }),
  setActiveWorkspace: (id) => set({ activeWorkspace: id }),
  setRechte: (rechte) => set({ rechte }),
  setProjekte: (projekte) => set({ projekte }),
  setObjektkopf: (objektkopf) => set({ objektkopf }),
  setUnterlage: (unterlage) => set({ unterlage }),
  aktualisiereUnterlage: (aktuelle) => set((s) => (s.unterlage ? { unterlage: { ...s.unterlage, aktuelle } } : s)),
  reset: () => set({ ...DEFAULTS }),
}));
