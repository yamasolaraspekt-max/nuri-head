/**
 * Hausplaner UI-2 — Kontext-Bauer für die Activation-Engine. REINE Funktion.
 *
 * Sammelt die Aktivierungs-Eingaben aus den drei getrennten Wahrheiten (UI-State: Werkzeug/
 * Arbeitsbereich · Modell-Store: Ansicht/Auswahl/Speicherstatus · Rechte) zu EINEM
 * `AktivierungsKontext`. Nimmt bereits extrahierte Primitive entgegen (kein Store-Import), damit
 * die Funktion ohne React/Store testbar bleibt.
 */

import type { AktivierungsKontext, ObjectType, ViewType, WorkspaceId } from './toolTypes';

export interface KontextEingabe {
  workspace: WorkspaceId;
  view: ViewType;
  /** Typen der aktuell ausgewählten Objekte (aus scene.nodes der Auswahl). */
  selectionTypes: ObjectType[];
  /** Zustände der Auswahl (z. B. 'locked'); Standard: leer. */
  selectionStates?: string[];
  /** Rechte des Nutzers, z. B. 'Hausplaner,update'. */
  permissions: string[];
  capabilities?: string[];
  /** 'editable' | 'readonly' | 'conflict' | 'offline'. Standard: 'editable'. */
  projectState?: string;
}

export function baueAktivierungsKontext(e: KontextEingabe): AktivierungsKontext {
  return {
    workspace: e.workspace,
    view: e.view,
    selection: {
      count: e.selectionTypes.length,
      types: e.selectionTypes,
      states: e.selectionStates ?? [],
    },
    permissions: e.permissions,
    capabilities: e.capabilities ?? [],
    projectState: e.projectState ?? 'editable',
  };
}
