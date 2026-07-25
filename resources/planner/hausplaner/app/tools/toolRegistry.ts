/**
 * Hausplaner UI-2 — Tool-Registry (Master-Prompt §22). REINE Daten + Lookups, kein React.
 *
 * EINE Wahrheit über die verfügbaren Werkzeuge. Bildet den heutigen Bestand ab (die sechs
 * Werkzeuge aus HausplanerApp: Auswahl/Wand/Fenster/Tür/Dach/Treppe) plus globale Aktionen (§6),
 * damit die Activation-Engine gegen echte Definitionen getestet werden kann. Der Editor (UI-3)
 * baut Toolbar/Tastatur künftig aus DIESER Registry statt aus der hartcodierten Werkzeug-Union.
 */

import type { ToolDefinition } from './toolTypes';

/** Bekannte Arbeitsbereiche (heute real: 'architektur'). */
export const WORKSPACE_ARCHITEKTUR = 'architektur';

/** Alle transformierbaren Objekttypen (für globale Transform-/Lösch-Werkzeuge). */
const AUSWAEHLBAR = ['wall', 'window', 'door', 'opening', 'zone', 'object', 'route', 'roof'] as const;

/**
 * Registry-Liste. Reihenfolge = Anzeigereihenfolge in der Werkzeugleiste.
 * Zeichenwerkzeuge sind an den Architektur-Workspace und 2D/Split gebunden (in 3D wird nicht
 * gezeichnet); Auswahl ist überall verfügbar.
 */
export const TOOL_DEFINITIONS: readonly ToolDefinition[] = [
  {
    id: 'auswahl',
    label: 'Auswahl',
    icon: 'auswahl',
    art: 'werkzeug',
    groupId: 'global',
    supportedWorkspaces: [],
    supportedViews: [],
    shortcut: 'V',
    helpText: 'Objekte anklicken zum Markieren, ziehen zum Bewegen.',
    // I2/AUF-31: Metadaten aus dem Werkzeugpaket zusammengeführt (Weg 1) — additiv,
    //   kein Bestandsfeld geändert. Das Paket-Werkzeug 'select' war dasselbe Werkzeug.
    meaning: 'Einzelne Objekte auswählen.',
    usageArea: 'Alle Workspaces; Ausgangspunkt jeder Bearbeitung.',
    group: 'Auswahl',
    tooltip: { title: 'Auswahl', body: 'Einzelne Objekte auswählen.', usage: 'Einsatzbereich: Alle Workspaces; Ausgangspunkt jeder Bearbeitung.' },
  },
  {
    id: 'wand',
    label: 'Wand',
    icon: 'wand',
    art: 'werkzeug',
    groupId: 'gebaeude',
    supportedWorkspaces: [WORKSPACE_ARCHITEKTUR],
    supportedViews: ['2d', 'split'],
    shortcut: 'W',
    bauteilKind: 'wall',
    helpText: 'Wände zeichnen — Punkt für Punkt klicken (Polygonzug).',
    disabledReasonDefault: 'Wände werden im Architektur-Arbeitsbereich gezeichnet.',
    // I2/AUF-31: Metadaten aus dem Werkzeugpaket zusammengeführt (Weg 1) — additiv,
    //   kein Bestandsfeld geändert. Das Paket-Werkzeug 'wall' war dasselbe Werkzeug.
    meaning: 'Parametrische Wand mit Aufbau, Höhe und Dicke erzeugen.',
    usageArea: 'Architektur, Bauphysik, Heizlast.',
    group: 'Architektur',
    tooltip: { title: 'Wand', body: 'Parametrische Wand mit Aufbau, Höhe und Dicke erzeugen.', usage: 'Einsatzbereich: Architektur, Bauphysik, Heizlast.' },
  },
  {
    id: 'fenster',
    label: 'Fenster',
    icon: 'fenster',
    art: 'werkzeug',
    groupId: 'oeffnungen',
    supportedWorkspaces: [WORKSPACE_ARCHITEKTUR],
    supportedViews: ['2d', 'split'],
    shortcut: 'F',
    bauteilKind: 'window',
    helpText: 'Fenster auf eine Wand setzen — Typ oben wählbar.',
    // I2/AUF-31: Metadaten aus dem Werkzeugpaket zusammengeführt (Weg 1) — additiv,
    //   kein Bestandsfeld geändert. Das Paket-Werkzeug 'window' war dasselbe Werkzeug.
    meaning: 'Fenster mit Profil, Glas und Öffnungsart platzieren.',
    usageArea: 'Architektur, Fassade, Heizlast.',
    group: 'Architektur',
    tooltip: { title: 'Fenster', body: 'Fenster mit Profil, Glas und Öffnungsart platzieren.', usage: 'Einsatzbereich: Architektur, Fassade, Heizlast.' },
  },
  {
    id: 'tuer',
    label: 'Tür',
    icon: 'tuer',
    art: 'werkzeug',
    groupId: 'oeffnungen',
    supportedWorkspaces: [WORKSPACE_ARCHITEKTUR],
    supportedViews: ['2d', 'split'],
    shortcut: 'T',
    bauteilKind: 'door',
    helpText: 'Tür auf eine Wand setzen — Typ oben wählbar.',
    // I2/AUF-31: Metadaten aus dem Werkzeugpaket zusammengeführt (Weg 1) — additiv,
    //   kein Bestandsfeld geändert. Das Paket-Werkzeug 'door' war dasselbe Werkzeug.
    meaning: 'Türöffnung und Türelement platzieren.',
    usageArea: 'Architektur, Bad, Küche.',
    group: 'Architektur',
    tooltip: { title: 'Tür', body: 'Türöffnung und Türelement platzieren.', usage: 'Einsatzbereich: Architektur, Bad, Küche.' },
  },
  {
    id: 'dach',
    label: 'Dach',
    icon: 'dach',
    art: 'werkzeug',
    groupId: 'gebaeude',
    supportedWorkspaces: [WORKSPACE_ARCHITEKTUR],
    supportedViews: ['2d', 'split'],
    shortcut: 'D',
    bauteilKind: 'roof',
    helpText: 'Dach über den Gebäudeumriss aufsetzen — dann in 3D betrachten.',
    // I2/AUF-31: Metadaten aus dem Werkzeugpaket zusammengeführt (Weg 1) — additiv,
    //   kein Bestandsfeld geändert. Das Paket-Werkzeug 'roof' war dasselbe Werkzeug.
    meaning: 'Dach aus Kontur oder Dachform erzeugen.',
    usageArea: 'Dachplanung und Bauphysik.',
    group: 'Architektur',
    tooltip: { title: 'Dach', body: 'Dach aus Kontur oder Dachform erzeugen.', usage: 'Einsatzbereich: Dachplanung und Bauphysik.' },
  },
  {
    id: 'decke',
    label: 'Decke',
    icon: 'decke',
    art: 'werkzeug',
    groupId: 'gebaeude',
    supportedWorkspaces: [WORKSPACE_ARCHITEKTUR],
    supportedViews: ['2d', 'split'],
    shortcut: 'K',
    bauteilKind: 'ceiling',
    helpText: 'Geschossdecke aus dem Grundriss aufsetzen (Treppen werden ausgespart) — Etagen-Basis.',
    // I2/AUF-31: Metadaten aus dem Werkzeugpaket zusammengeführt (Weg 1) — additiv,
    //   kein Bestandsfeld geändert. Das Paket-Werkzeug 'slab' war dasselbe Werkzeug.
    meaning: 'Massive oder mehrschichtige Decke erzeugen.',
    usageArea: 'Architektur, Statik, Heizlast.',
    group: 'Architektur',
    tooltip: { title: 'Decke / Bodenplatte', body: 'Massive oder mehrschichtige Decke erzeugen.', usage: 'Einsatzbereich: Architektur, Statik, Heizlast.' },
  },
  {
    id: 'treppe',
    label: 'Treppe',
    icon: 'treppe',
    art: 'werkzeug',
    groupId: 'erschliessung',
    supportedWorkspaces: [WORKSPACE_ARCHITEKTUR],
    supportedViews: ['2d', 'split'],
    shortcut: 'R',
    bauteilKind: 'stair',
    helpText: 'Treppe setzen — zwei Klicks: Lauflinie Anfang→Ende (DIN 18065).',
    // I2/AUF-31: Metadaten aus dem Werkzeugpaket zusammengeführt (Weg 1) — additiv,
    //   kein Bestandsfeld geändert. Das Paket-Werkzeug 'stairs' war dasselbe Werkzeug.
    meaning: 'Treppe zwischen Ebenen parametrisch erzeugen.',
    usageArea: 'Erschließung und Schnittplanung.',
    group: 'Architektur',
    tooltip: { title: 'Treppe', body: 'Treppe zwischen Ebenen parametrisch erzeugen.', usage: 'Einsatzbereich: Erschließung und Schnittplanung.' },
  },

  // --- Globale Aktionen (§6): keine Zeichenwerkzeuge, aber Teil der Registry, damit die
  //     Activation-Engine Auswahl-/Rechte-Bedingungen datengetrieben liefert. ---
  {
    id: 'loeschen',
    label: 'Löschen',
    icon: 'loeschen',
    art: 'aktion',
    groupId: 'global',
    supportedWorkspaces: [],
    supportedViews: [],
    shortcut: 'Delete',
    minSelectionCount: 1,
    requiredPermissions: ['Hausplaner,update'],
    supportedSelectionTypes: [...AUSWAEHLBAR],
    helpText: 'Ausgewählte Objekte löschen (per Undo umkehrbar).',
    activationRules: [
      { type: 'object-state', operator: 'not-equals', value: 'locked', grund: 'Ein gesperrtes Objekt kann nicht gelöscht werden.' },
      { type: 'project-state', operator: 'not-equals', value: 'readonly', grund: 'Der Plan ist schreibgeschützt.' },
    ],
    // I2/AUF-31: Metadaten aus dem Werkzeugpaket zusammengeführt (Weg 1) — additiv,
    //   kein Bestandsfeld geändert. Das Paket-Werkzeug 'delete' war dasselbe Werkzeug.
    meaning: 'Auswahl nach Abhängigkeitsprüfung entfernen.',
    usageArea: 'Alle bearbeitbaren Objekte.',
    group: 'Bearbeiten',
    tooltip: { title: 'Löschen', body: 'Auswahl nach Abhängigkeitsprüfung entfernen.', usage: 'Einsatzbereich: Alle bearbeitbaren Objekte.' },
  },
  {
    id: 'duplizieren',
    label: 'Duplizieren',
    icon: 'duplizieren',
    art: 'aktion',
    groupId: 'global',
    supportedWorkspaces: [],
    supportedViews: [],
    shortcut: 'Ctrl+D',
    minSelectionCount: 1,
    requiredPermissions: ['Hausplaner,update'],
    helpText: 'Ausgewählte Objekte kopieren (versetzt).',
    activationRules: [
      { type: 'project-state', operator: 'not-equals', value: 'readonly', grund: 'Der Plan ist schreibgeschützt.' },
    ],
    // I2/AUF-31: Metadaten aus dem Werkzeugpaket zusammengeführt (Weg 1) — additiv,
    //   kein Bestandsfeld geändert. Das Paket-Werkzeug 'duplicate' war dasselbe Werkzeug.
    meaning: 'Sofortige Kopie am aktuellen Ort erzeugen.',
    usageArea: 'Fenster, Möbel, technische Objekte.',
    group: 'Bearbeiten',
    tooltip: { title: 'Duplizieren', body: 'Sofortige Kopie am aktuellen Ort erzeugen.', usage: 'Einsatzbereich: Fenster, Möbel, technische Objekte.' },
  },
];

const BY_ID = new Map(TOOL_DEFINITIONS.map((t) => [t.id, t]));
const BY_SHORTCUT = new Map(
  TOOL_DEFINITIONS.filter((t) => t.shortcut).map((t) => [t.shortcut!.toLowerCase(), t]),
);

/** Werkzeug nach id (oder undefined). */
export function toolNach(id: string): ToolDefinition | undefined {
  return BY_ID.get(id);
}

/** Werkzeug zu einem Tastenkürzel (case-insensitiv), oder undefined. */
export function toolFuerShortcut(shortcut: string): ToolDefinition | undefined {
  return BY_SHORTCUT.get(shortcut.toLowerCase());
}

/** Alle Werkzeuge, optional nach Gruppe gefiltert. */
export function alleTools(groupId?: string): ToolDefinition[] {
  return groupId ? TOOL_DEFINITIONS.filter((t) => t.groupId === groupId) : [...TOOL_DEFINITIONS];
}

/** Nur die modus-schaltenden Werkzeuge der Werkzeugleiste (art='werkzeug'), in Anzeigereihenfolge. */
export function werkzeugTools(): ToolDefinition[] {
  return TOOL_DEFINITIONS.filter((t) => t.art === 'werkzeug');
}

/** Findet Shortcut-Kollisionen (zwei Werkzeuge, gleiches Kürzel). Für §29 Konfliktprüfung. */
export function shortcutKollisionen(): Array<{ shortcut: string; ids: string[] }> {
  const map = new Map<string, string[]>();
  for (const t of TOOL_DEFINITIONS) {
    if (!t.shortcut) continue;
    const key = t.shortcut.toLowerCase();
    map.set(key, [...(map.get(key) ?? []), t.id]);
  }
  return [...map.entries()].filter(([, ids]) => ids.length > 1).map(([shortcut, ids]) => ({ shortcut, ids }));
}
