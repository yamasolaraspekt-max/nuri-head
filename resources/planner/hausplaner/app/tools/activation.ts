/**
 * Hausplaner UI-2 — Activation-Engine (Master-Prompt §21). REINE Funktion, ohne DOM/React.
 *
 * `resolveToolState(tool, ctx)` leitet aus fachlichen Bedingungen ab, OB ein Werkzeug aktiv ist,
 * und liefert bei Deaktivierung einen menschlichen GRUND (für Tooltip; §28: nicht nur Farbe).
 * Ein Werkzeug wird NIE allein durch visuelles Ausblenden deaktiviert — die Wahrheit ist diese
 * Funktion. Voll unit-testbar (eigene Testdatei mit roten Gegenproben).
 */

import type {
  AktivierungsKontext,
  ObjectType,
  ToolActivationRule,
  ToolDefinition,
  WerkzeugZustand,
} from './toolTypes';

const aktiv: WerkzeugZustand = { enabled: true, reason: null };
const inaktiv = (reason: string): WerkzeugZustand => ({ enabled: false, reason });

/** Wertet eine einzelne Regel aus. true = erfüllt (Werkzeug bleibt aktiv). */
function regelErfuellt(rule: ToolActivationRule, ctx: AktivierungsKontext): boolean {
  const wert = rule.value;
  switch (rule.type) {
    case 'workspace':
      return vergleich(ctx.workspace, rule.operator, wert);
    case 'view':
      return vergleich(ctx.view, rule.operator, wert);
    case 'project-state':
      return vergleich(ctx.projectState, rule.operator, wert);
    case 'selection-count':
      return vergleich(ctx.selection.count, rule.operator, wert);
    case 'selection-type':
      return listenRegel(ctx.selection.types, rule.operator, wert);
    case 'object-state':
      return listenRegel(ctx.selection.states, rule.operator, wert);
    case 'permission':
      return listenRegel(ctx.permissions, rule.operator, wert);
    case 'capability':
      return listenRegel(ctx.capabilities, rule.operator, wert);
    default:
      return true;
  }
}

/** Skalarvergleich (workspace/view/project-state/selection-count). */
function vergleich(ist: string | number, op: ToolActivationRule['operator'], soll: unknown): boolean {
  switch (op) {
    case 'equals':
      return ist === soll;
    case 'not-equals':
      return ist !== soll;
    case 'greater-than':
      return typeof ist === 'number' && typeof soll === 'number' && ist > soll;
    case 'less-than':
      return typeof ist === 'number' && typeof soll === 'number' && ist < soll;
    case 'contains':
      return String(ist).includes(String(soll));
    default:
      return true;
  }
}

/** Listenregel (selection-type/object-state/permission/capability). */
function listenRegel(liste: readonly string[], op: ToolActivationRule['operator'], soll: unknown): boolean {
  const s = String(soll);
  switch (op) {
    case 'contains':
      return liste.includes(s);
    case 'not-equals':
      return !liste.includes(s);
    case 'equals':
      return liste.length > 0 && liste.every((x) => x === s);
    case 'greater-than':
      return typeof soll === 'number' && liste.length > soll;
    case 'less-than':
      return typeof soll === 'number' && liste.length < soll;
    default:
      return true;
  }
}

/**
 * Zentrale Ableitung. Reihenfolge: Workspace → Ansicht → Rechte → Fähigkeiten → Auswahlanzahl →
 * Auswahltyp → freie Regeln. Erste verletzte Bedingung liefert den Grund.
 */
export function resolveToolState(tool: ToolDefinition, ctx: AktivierungsKontext): WerkzeugZustand {
  if (tool.supportedWorkspaces.length > 0 && !tool.supportedWorkspaces.includes(ctx.workspace)) {
    return inaktiv(tool.disabledReasonDefault ?? `„${tool.label}“ ist im aktuellen Arbeitsbereich nicht verfügbar.`);
  }
  if (tool.supportedViews.length > 0 && !tool.supportedViews.includes(ctx.view)) {
    return inaktiv(`„${tool.label}“ ist in dieser Ansicht nicht verfügbar.`);
  }
  for (const p of tool.requiredPermissions ?? []) {
    if (!ctx.permissions.includes(p)) {
      return inaktiv(`Keine Berechtigung für „${tool.label}“.`);
    }
  }
  for (const c of tool.requiredCapabilities ?? []) {
    if (!ctx.capabilities.includes(c)) {
      return inaktiv(`„${tool.label}“ ist hier technisch nicht verfügbar.`);
    }
  }
  if (tool.minSelectionCount !== undefined && ctx.selection.count < tool.minSelectionCount) {
    return inaktiv(
      tool.minSelectionCount === 1
        ? `„${tool.label}“ braucht eine Auswahl.`
        : `„${tool.label}“ braucht mindestens ${tool.minSelectionCount} ausgewählte Objekte.`,
    );
  }
  if (tool.maxSelectionCount !== undefined && ctx.selection.count > tool.maxSelectionCount) {
    return inaktiv(`„${tool.label}“ unterstützt höchstens ${tool.maxSelectionCount} Objekte.`);
  }
  if (tool.supportedSelectionTypes && ctx.selection.count > 0) {
    const erlaubt = new Set<ObjectType>(tool.supportedSelectionTypes);
    if (!ctx.selection.types.every((t) => erlaubt.has(t))) {
      return inaktiv(`„${tool.label}“ ist für den ausgewählten Objekttyp nicht verfügbar.`);
    }
  }
  for (const rule of tool.activationRules ?? []) {
    if (!regelErfuellt(rule, ctx)) {
      return inaktiv(rule.grund);
    }
  }
  return aktiv;
}
