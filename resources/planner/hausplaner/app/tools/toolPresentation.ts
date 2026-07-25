/**
 * Wizard-Welle A1 — Werkzeug-Präsentationsschicht. REINE Daten + reine Funktionen, kein React.
 *
 * Zweck: Die Kuratierung „welches Werkzeug erscheint wo" war bisher als lokale Konstante
 * (`CAD_TEILMENGE` in `faehigkeiten.ts`) im Nebensatz einer UI-nahen Datei versteckt. Hier steht
 * sie als benannte, getestete Datenschicht — für JEDES Werkzeug aus Registry ODER Katalog genau
 * eine Zone und eine Begründung.
 *
 * Abgrenzung (keine zweite Wahrheit):
 *  - WELCHE Werkzeuge es gibt        → `toolRegistry.TOOL_DEFINITIONS` / `toolCatalog.TOOL_KATALOG`
 *  - OB ein Werkzeug bedienbar ist   → `activation.resolveToolState` (Grund aus `ToolActivationRule.grund`)
 *  - WO es erscheint                 → diese Datei
 * Es wird kein Katalog-Eintrag gelöscht: `versteckt` ist eine Regel, kein Datenverlust — der Rückweg
 * bleibt offen, falls ein Werkzeug doch gebraucht wird.
 *
 * Erweiterungspunkt (A2): Diese Regeln sind der System-Default. Eine persönliche Ebene
 * (Pinnen/Workspace-Preset) legt sich später DARÜBER (persönlich → Preset → System-Default) und
 * ersetzt diese Datei nicht.
 */
import type { ToolDefinition } from './toolTypes';
import { TOOL_DEFINITIONS, toolNach } from './toolRegistry';
import { TOOL_KATALOG, katalogTool } from './toolCatalog';

/**
 * Zone der Werkzeugleiste:
 * - 'fix'       = immer sichtbare Bau-Werkzeuge (Rail-Kopf)
 * - 'kontext'   = Sofortbefehle auf die Auswahl
 * - 'weitere'   = kuratiert verfügbar, Handler folgt (A2/A3)
 * - 'versteckt' = bewusst nicht angeboten (Produkt-Scope), Daten bleiben erhalten
 */
export type RailZone = 'fix' | 'kontext' | 'weitere' | 'versteckt';

/** Woher die Werkzeug-Definition stammt (Registry hat bei Namensgleichheit Vorrang). */
export type ToolHerkunft = 'registry' | 'katalog';

export interface ToolPresentationRule {
  toolId: string;
  zone: RailZone;
  /** Anzeigereihenfolge innerhalb der Zone (aufsteigend, stabil). */
  ordnung: number;
  herkunft: ToolHerkunft;
  /** Warum diese Zone — erscheint im Konfigurationsdialog (A3) und in der Abnahme. */
  begruendung: string;
}

const GRUND_BAU = 'Bau-Werkzeug, Bestand';
const GRUND_SOFORT = 'Sofortbefehl auf die Auswahl';
const GRUND_CAD = 'CAD-tauglich, Handler folgt (A2/A3)';
const GRUND_DTP = 'DTP/Layout — kein Bau-Werkzeug (Produkt-Scope)';

/**
 * Der Default-Regelsatz = exakt der gemessene Ist-Zustand (keine neue Fachentscheidung).
 * 9 Registry-ids + 54 Katalog-ids = 63 Regeln. Welche CAD-Werkzeuge ein Bauplaner wirklich in der
 * Leiste braucht, entscheidet Yama (Fach-Freigabe) — A1 baut nur den Mechanismus.
 */
export const TOOL_PRESENTATION_RULES: readonly ToolPresentationRule[] = [
  // --- Registry: die 7 modus-schaltenden Bau-Werkzeuge (Reihenfolge = Registry-Reihenfolge) ------
  { toolId: 'auswahl', zone: 'fix', ordnung: 1, herkunft: 'registry', begruendung: GRUND_BAU },
  { toolId: 'wand', zone: 'fix', ordnung: 2, herkunft: 'registry', begruendung: GRUND_BAU },
  { toolId: 'fenster', zone: 'fix', ordnung: 3, herkunft: 'registry', begruendung: GRUND_BAU },
  { toolId: 'tuer', zone: 'fix', ordnung: 4, herkunft: 'registry', begruendung: GRUND_BAU },
  { toolId: 'dach', zone: 'fix', ordnung: 5, herkunft: 'registry', begruendung: GRUND_BAU },
  { toolId: 'decke', zone: 'fix', ordnung: 6, herkunft: 'registry', begruendung: GRUND_BAU },
  { toolId: 'treppe', zone: 'fix', ordnung: 7, herkunft: 'registry', begruendung: GRUND_BAU },

  // --- Registry: die 2 Sofort-Aktionen ----------------------------------------------------------
  { toolId: 'loeschen', zone: 'kontext', ordnung: 1, herkunft: 'registry', begruendung: GRUND_SOFORT },
  { toolId: 'duplizieren', zone: 'kontext', ordnung: 2, herkunft: 'registry', begruendung: GRUND_SOFORT },

  // --- Katalog: die 15 heute kuratierten CAD-ids (Reihenfolge = bisherige `CAD_TEILMENGE`) -------
  { toolId: 'rotate', zone: 'weitere', ordnung: 1, herkunft: 'katalog', begruendung: GRUND_CAD },
  { toolId: 'scale', zone: 'weitere', ordnung: 2, herkunft: 'katalog', begruendung: GRUND_CAD },
  { toolId: 'free-transform', zone: 'weitere', ordnung: 3, herkunft: 'katalog', begruendung: GRUND_CAD },
  { toolId: 'align-left', zone: 'weitere', ordnung: 4, herkunft: 'katalog', begruendung: GRUND_CAD },
  { toolId: 'align-center', zone: 'weitere', ordnung: 5, herkunft: 'katalog', begruendung: GRUND_CAD },
  { toolId: 'align-right', zone: 'weitere', ordnung: 6, herkunft: 'katalog', begruendung: GRUND_CAD },
  { toolId: 'align-top', zone: 'weitere', ordnung: 7, herkunft: 'katalog', begruendung: GRUND_CAD },
  { toolId: 'align-middle', zone: 'weitere', ordnung: 8, herkunft: 'katalog', begruendung: GRUND_CAD },
  { toolId: 'align-bottom', zone: 'weitere', ordnung: 9, herkunft: 'katalog', begruendung: GRUND_CAD },
  { toolId: 'distribute-horizontal', zone: 'weitere', ordnung: 10, herkunft: 'katalog', begruendung: GRUND_CAD },
  { toolId: 'distribute-vertical', zone: 'weitere', ordnung: 11, herkunft: 'katalog', begruendung: GRUND_CAD },
  { toolId: 'hand', zone: 'weitere', ordnung: 12, herkunft: 'katalog', begruendung: GRUND_CAD },
  { toolId: 'zoom', zone: 'weitere', ordnung: 13, herkunft: 'katalog', begruendung: GRUND_CAD },
  { toolId: 'measure', zone: 'weitere', ordnung: 14, herkunft: 'katalog', begruendung: GRUND_CAD },
  { toolId: 'layers-panel', zone: 'weitere', ordnung: 15, herkunft: 'katalog', begruendung: GRUND_CAD },

  // --- Katalog: die 39 übrigen ids, EINZELN aufgeführt (die Liste ist der prüfbare Beweis —
  //     keine Negativ-Ableitung zur Laufzeit). Reihenfolge = Katalog-Reihenfolge. ----------------
  { toolId: 'selection', zone: 'versteckt', ordnung: 1, herkunft: 'katalog', begruendung: GRUND_DTP },
  { toolId: 'direct-selection', zone: 'versteckt', ordnung: 2, herkunft: 'katalog', begruendung: GRUND_DTP },
  { toolId: 'page', zone: 'versteckt', ordnung: 3, herkunft: 'katalog', begruendung: GRUND_DTP },
  { toolId: 'gap', zone: 'versteckt', ordnung: 4, herkunft: 'katalog', begruendung: GRUND_DTP },
  { toolId: 'type', zone: 'versteckt', ordnung: 5, herkunft: 'katalog', begruendung: GRUND_DTP },
  { toolId: 'line', zone: 'versteckt', ordnung: 6, herkunft: 'katalog', begruendung: GRUND_DTP },
  { toolId: 'pen', zone: 'versteckt', ordnung: 7, herkunft: 'katalog', begruendung: GRUND_DTP },
  { toolId: 'add-anchor', zone: 'versteckt', ordnung: 8, herkunft: 'katalog', begruendung: GRUND_DTP },
  { toolId: 'delete-anchor', zone: 'versteckt', ordnung: 9, herkunft: 'katalog', begruendung: GRUND_DTP },
  { toolId: 'convert-anchor', zone: 'versteckt', ordnung: 10, herkunft: 'katalog', begruendung: GRUND_DTP },
  { toolId: 'pencil', zone: 'versteckt', ordnung: 11, herkunft: 'katalog', begruendung: GRUND_DTP },
  { toolId: 'smooth', zone: 'versteckt', ordnung: 12, herkunft: 'katalog', begruendung: GRUND_DTP },
  { toolId: 'erase-path', zone: 'versteckt', ordnung: 13, herkunft: 'katalog', begruendung: GRUND_DTP },
  { toolId: 'rectangle-frame', zone: 'versteckt', ordnung: 14, herkunft: 'katalog', begruendung: GRUND_DTP },
  { toolId: 'ellipse-frame', zone: 'versteckt', ordnung: 15, herkunft: 'katalog', begruendung: GRUND_DTP },
  { toolId: 'polygon-frame', zone: 'versteckt', ordnung: 16, herkunft: 'katalog', begruendung: GRUND_DTP },
  { toolId: 'rectangle', zone: 'versteckt', ordnung: 17, herkunft: 'katalog', begruendung: GRUND_DTP },
  { toolId: 'ellipse', zone: 'versteckt', ordnung: 18, herkunft: 'katalog', begruendung: GRUND_DTP },
  { toolId: 'polygon', zone: 'versteckt', ordnung: 19, herkunft: 'katalog', begruendung: GRUND_DTP },
  { toolId: 'scissors', zone: 'versteckt', ordnung: 20, herkunft: 'katalog', begruendung: GRUND_DTP },
  { toolId: 'shear', zone: 'versteckt', ordnung: 21, herkunft: 'katalog', begruendung: GRUND_DTP },
  { toolId: 'note', zone: 'versteckt', ordnung: 22, herkunft: 'katalog', begruendung: GRUND_DTP },
  { toolId: 'eyedropper', zone: 'versteckt', ordnung: 23, herkunft: 'katalog', begruendung: GRUND_DTP },
  { toolId: 'fill', zone: 'versteckt', ordnung: 24, herkunft: 'katalog', begruendung: GRUND_DTP },
  { toolId: 'stroke', zone: 'versteckt', ordnung: 25, herkunft: 'katalog', begruendung: GRUND_DTP },
  { toolId: 'swap-fill-stroke', zone: 'versteckt', ordnung: 26, herkunft: 'katalog', begruendung: GRUND_DTP },
  { toolId: 'default-fill-stroke', zone: 'versteckt', ordnung: 27, herkunft: 'katalog', begruendung: GRUND_DTP },
  { toolId: 'format-container', zone: 'versteckt', ordnung: 28, herkunft: 'katalog', begruendung: GRUND_DTP },
  { toolId: 'normal-screen', zone: 'versteckt', ordnung: 29, herkunft: 'katalog', begruendung: GRUND_DTP },
  { toolId: 'preview-screen', zone: 'versteckt', ordnung: 30, herkunft: 'katalog', begruendung: GRUND_DTP },
  { toolId: 'reference-point', zone: 'versteckt', ordnung: 31, herkunft: 'katalog', begruendung: GRUND_DTP },
  { toolId: 'link-proportions', zone: 'versteckt', ordnung: 32, herkunft: 'katalog', begruendung: GRUND_DTP },
  { toolId: 'object-style', zone: 'versteckt', ordnung: 33, herkunft: 'katalog', begruendung: GRUND_DTP },
  { toolId: 'pages-panel', zone: 'versteckt', ordnung: 34, herkunft: 'katalog', begruendung: GRUND_DTP },
  { toolId: 'swatches-panel', zone: 'versteckt', ordnung: 35, herkunft: 'katalog', begruendung: GRUND_DTP },
  { toolId: 'preflight', zone: 'versteckt', ordnung: 36, herkunft: 'katalog', begruendung: GRUND_DTP },
  { toolId: 'search', zone: 'versteckt', ordnung: 37, herkunft: 'katalog', begruendung: GRUND_DTP },
  { toolId: 'settings', zone: 'versteckt', ordnung: 38, herkunft: 'katalog', begruendung: GRUND_DTP },
  { toolId: 'more', zone: 'versteckt', ordnung: 39, herkunft: 'katalog', begruendung: GRUND_DTP },
];

const REGEL_NACH_ID = new Map(TOOL_PRESENTATION_RULES.map((r) => [r.toolId, r]));

/** Die Regel eines Werkzeugs (oder undefined, wenn es keine gibt). */
export function praesentation(toolId: string): ToolPresentationRule | undefined {
  return REGEL_NACH_ID.get(toolId);
}

/**
 * Löst eine id gegen Registry (Vorrang) und Katalog auf.
 * Vorrang der Registry ist Absicht: bei echter Namensgleichheit gilt die gepflegte Werkzeug-Wahrheit;
 * verschiedene ids (z. B. Registry 'auswahl' vs. Katalog 'selection') bleiben verschieden — sie werden
 * hier NICHT heimlich vereinheitlicht.
 */
function loeseAuf(toolId: string): ToolDefinition | undefined {
  return toolNach(toolId) ?? katalogTool(toolId);
}

/** Kern von `zoneTools`, gegen einen beliebigen Regelsatz (für Gegenproben in Tests). */
export function zoneToolsIn(
  regeln: readonly ToolPresentationRule[],
  zone: RailZone,
): ToolDefinition[] {
  return regeln
    .map((regel, index) => ({ regel, index }))
    .filter(({ regel }) => regel.zone === zone)
    // Stabil: primär `ordnung`, bei Gleichstand die Regel-Reihenfolge — nie nach `id`,
    // sonst springt die Leiste bei jeder Umbenennung.
    .sort((a, b) => (a.regel.ordnung - b.regel.ordnung) || (a.index - b.index))
    .map(({ regel }) => loeseAuf(regel.toolId))
    // Unbekannte id wird ausgelassen, nicht geworfen — `verwaisteRegeln()` macht sie sichtbar.
    .filter((t): t is ToolDefinition => t !== undefined);
}

/** Die Werkzeuge einer Zone, in Anzeigereihenfolge. Leere Zone ⇒ leeres Array (nie undefined). */
export function zoneTools(zone: RailZone): ToolDefinition[] {
  return zoneToolsIn(TOOL_PRESENTATION_RULES, zone);
}

/** Kern von `verwaisteRegeln`, gegen einen beliebigen Regelsatz (für Gegenproben in Tests). */
export function verwaisteRegelnIn(regeln: readonly ToolPresentationRule[]): string[] {
  return regeln.filter((r) => loeseAuf(r.toolId) === undefined).map((r) => r.toolId);
}

/** ids in Regeln, die weder in der Registry noch im Katalog existieren — muss leer sein. */
export function verwaisteRegeln(): string[] {
  return verwaisteRegelnIn(TOOL_PRESENTATION_RULES);
}

/** ids aus Registry/Katalog ohne Regel — muss leer sein (jedes Werkzeug hat genau eine Zone). */
export function regelloseWerkzeuge(): string[] {
  const alle = [...TOOL_DEFINITIONS.map((t) => t.id), ...TOOL_KATALOG.map((t) => t.id)];
  return [...new Set(alle.filter((id) => !REGEL_NACH_ID.has(id)))];
}
