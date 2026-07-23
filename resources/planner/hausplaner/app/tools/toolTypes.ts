/**
 * Hausplaner UI-2 — Werkzeug-Datenmodell (Tool Registry, Master-Prompt §22).
 *
 * REINE Typen + Verträge, kein React/Konva/three, keine Szene-Mutation. Definiert, welche
 * Werkzeuge es gibt und UNTER WELCHEN BEDINGUNGEN sie aktiv sind — datengetrieben statt in der
 * UI verstreut. Der Editor (UI-3) liest diese Registry; hier steht nur das Modell.
 *
 * Abgrenzung: `geometry/werkzeugRegistry.ts` beschreibt BAUTEILE (Parametrik/Fähigkeiten). Diese
 * Datei beschreibt WERKZEUGE (Bedienung/Aktivierung). Ein Werkzeug KANN ein Bauteil referenzieren
 * (`bauteilKind`), dupliziert es aber nicht.
 */

/** Arbeitsbereich (§9). Offen erweiterbar; heute real: 'architektur'. */
export type WorkspaceId = string;

/** Ansicht (§18). Deckt aktuell die Store-Ansicht (2d/split/3d); erweiterbar. */
export type ViewType = '2d' | 'split' | '3d' | 'wandansicht' | 'schnitt' | 'fassade' | 'planblatt';

/** Objekttyp der Auswahl — an unsere SceneNode-Typen angelehnt. */
export type ObjectType = 'wall' | 'window' | 'door' | 'opening' | 'zone' | 'object' | 'route' | 'roof';

/** Regeltyp (§22). */
export type ToolActivationRuleType =
  | 'workspace'
  | 'view'
  | 'selection-type'
  | 'selection-count'
  | 'permission'
  | 'object-state'
  | 'project-state'
  | 'capability';

export type ToolActivationOperator =
  | 'equals'
  | 'not-equals'
  | 'contains'
  | 'greater-than'
  | 'less-than';

/** Eine Aktivierungsregel; `grund` erklärt die Deaktivierung (Tooltip, §21/§28 — nicht nur Farbe). */
export interface ToolActivationRule {
  type: ToolActivationRuleType;
  operator: ToolActivationOperator;
  value: unknown;
  /** Menschlicher Grund, wenn diese Regel die Deaktivierung auslöst. */
  grund: string;
}

/**
 * Art des Werkzeugs:
 * - 'werkzeug' = modus-schaltendes Werkzeug (steht in der Werkzeugleiste, setzt `activeToolId`).
 * - 'aktion'   = Sofort-Befehl auf die Auswahl (Löschen/Duplizieren) — kein Modus, kein Toolbar-Eintrag.
 */
export type ToolArt = 'werkzeug' | 'aktion';

/** Werkzeugdefinition (§22). Leere `supportedWorkspaces`/`supportedViews` = überall zulässig. */
export interface ToolDefinition {
  id: string;
  label: string;
  /** Toolbar-Icon-Kennung (bestehendes svgWrap/werkzeugIcon-System). */
  icon: string;
  groupId: string;
  art: ToolArt;

  supportedWorkspaces: WorkspaceId[];
  supportedViews: ViewType[];
  /** Wenn gesetzt und etwas ausgewählt ist: jeder ausgewählte Typ muss enthalten sein. */
  supportedSelectionTypes?: ObjectType[];

  minSelectionCount?: number;
  maxSelectionCount?: number;

  requiredPermissions?: string[];
  requiredCapabilities?: string[];

  activationRules?: ToolActivationRule[];

  shortcut?: string;
  cursor?: string;

  /** Optional: das von diesem Werkzeug platzierte Bauteil (geometry/werkzeugRegistry-`kind`). */
  bauteilKind?: string;

  helpText: string;
  /** Grund, wenn generell nicht anwendbar (Fallback, falls keine spezifische Regel greift). */
  disabledReasonDefault?: string;
}

/** Kontext, gegen den die Aktivierung ausgewertet wird (aus UI-State + Modell-Store + Rechten). */
export interface AktivierungsKontext {
  workspace: WorkspaceId;
  view: ViewType;
  selection: {
    count: number;
    /** Typen der aktuell ausgewählten Objekte. */
    types: ObjectType[];
    /** Zustände der Auswahl (z. B. 'locked', 'released', 'foreign'). */
    states: string[];
  };
  permissions: string[];
  capabilities: string[];
  /** Projektzustand: 'editable' | 'readonly' | 'conflict' | 'offline' … */
  projectState: string;
}

/** Ergebnis der Aktivierung: aktiv? und (bei inaktiv) der Grund. */
export interface WerkzeugZustand {
  enabled: boolean;
  reason: string | null;
}
