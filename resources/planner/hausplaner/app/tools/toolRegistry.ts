/**
 * Hausplaner UI-2 — Tool-Registry (Master-Prompt §22). REINE Daten + Lookups, kein React.
 *
 * EINE Wahrheit über die verfügbaren Werkzeuge. Bildet den heutigen Bestand ab (die sechs
 * Werkzeuge aus HausplanerApp: Auswahl/Wand/Fenster/Tür/Dach/Treppe) plus globale Aktionen (§6),
 * damit die Activation-Engine gegen echte Definitionen getestet werden kann. Der Editor (UI-3)
 * baut Toolbar/Tastatur künftig aus DIESER Registry statt aus der hartcodierten Werkzeug-Union.
 */

import type { ToolDefinition } from './toolTypes';

/**
 * Bekannte Arbeitsbereiche — die Ids wohnen seit 03.08.2026 in `workspaceIds.ts`.
 *
 * *Sie standen hier, bis W-05 zeigte, dass dieser Ort einen Import-Kreis erzwingt
 * (toolRegistry -> paketAdapter -> arbeitsbereiche -> hierher). Die Re-Ausfuhr bleibt,
 * damit bestehende Aufrufer unveraendert weiterlesen; NEUE Traeger nehmen `workspaceIds`
 * direkt — und `dashboard/arbeitsbereiche.ts` MUSS es, sonst kehrt der Kreis zurueck.*
 */
export {
  WORKSPACE_IMPORT, WORKSPACE_ARCHITEKTUR, WORKSPACE_BAUPHYSIK,
  WORKSPACE_HEIZUNG, WORKSPACE_ELEKTRO_PV,
} from './workspaceIds';
import {
  WORKSPACE_ARCHITEKTUR, WORKSPACE_BAUPHYSIK,
  WORKSPACE_HEIZUNG, WORKSPACE_ELEKTRO_PV, WORKSPACE_IMPORT,
} from './workspaceIds';

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
    // AUF-35a: sichtbar heisst das Werkzeug **Markieren** (Yamas Referenz vom 25.07.). Die id
    // bleibt `auswahl`, das Kuerzel bleibt `V` — beides ist gespeicherte bzw. eingeuebte Wahrheit
    // und haette ohne Not 101 Icon-Dateinamen und jede Tastaturgewohnheit angefasst.
    label: 'Markieren',
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

  // --- W-05: aus dem Paket in die Leiste GEHOBEN ------------------------------------------
  //
  // **Von Hand eingetragen, nicht aus dem Paket abgeleitet — und das ist Absicht.**
  // Eine Ableitung braeuchte `import { PAKET_ALS_TOOLS } from './paketAdapter'`, und genau die
  // Kante schliesst einen Kreis: `paketAdapter` liest `TOOL_DEFINITIONS` und wertet
  // `PAKET_ALS_TOOLS` auf Modulebene aus (gefahren: `ReferenceError: Cannot access
  // 'TOOL_DEFINITIONS' before initialization`, paketAdapter:53). *Diese Datei importiert
  // deshalb NICHTS ausser Typen — sie ist das Blatt, an dem keine Kette zurueckfuehrt.*
  //
  // **Der Preis, offen benannt:** `label`, `icon` und `variante` stehen jetzt zweimal — hier
  // und im Paket. Dagegen steht eine Zusage, die die beiden Seiten FELD FUER FELD vergleicht
  // und rot wird, sobald sie auseinanderlaufen. *Der Test darf beide importieren; er ist ein
  // Blatt, kein Modul im Kreis.* So kostet die Doppelung Konsistenz nichts.
  {
    id: 'bemassen',
    label: 'Bemaßen',
    icon: 'bemassen',
    art: 'werkzeug',
    groupId: 'messen',
    supportedWorkspaces: [],
    supportedViews: ['2d', 'split'],
    helpText: 'Persistente technische Maßlinie erzeugen — zwei Punkte klicken.',
    meaning: 'Persistente technische Maßlinie erzeugen.',
    usageArea: '2D, Schnitt, Fassade.',
    group: 'Messen',
    tooltip: { title: 'Bemaßen', body: 'Persistente technische Maßlinie erzeugen.', usage: 'Einsatzbereich: 2D, Schnitt, Fassade.' },
  },
  {
    id: 'flaeche-messen',
    label: 'Fläche messen',
    icon: 'flaeche-messen',
    art: 'werkzeug',
    groupId: 'messen',
    supportedWorkspaces: [],
    supportedViews: ['2d', 'split'],
    helpText: 'Geschlossene Fläche ermitteln — Punkte klicken, letzter Punkt schliesst.',
    meaning: 'Geschlossene Fläche ermitteln.',
    usageArea: 'Räume, Fassaden, Dächer.',
    group: 'Messen',
    tooltip: { title: 'Fläche messen', body: 'Geschlossene Fläche ermitteln.', usage: 'Einsatzbereich: Räume, Fassaden, Dächer.' },
  },

  // --- Globale Aktionen (§6): keine Zeichenwerkzeuge, aber Teil der Registry, damit die
  //     Activation-Engine Auswahl-/Rechte-Bedingungen datengetrieben liefert. ---
  {
    // Z-05-N1 — **das Konturwerkzeug, jetzt erreichbar.**
    //
    // Z-05 hat es gebaut; der Eintrag hier fehlte, und ohne ihn gab es weder Knopf noch Kuerzel.
    // Ein achter `art: 'werkzeug'`-Eintrag ist **keine additive Zeile**: er bewegt die Fix-Zone
    // (7 -> 8), braucht eine Themen-Bindung, einen Vertrag und eine Praesentationsregel. Genau
    // deshalb ist er ein eigenes Blatt geworden und nicht in Z-05 mitgelaufen.
    //
    // **Das Icon heisst `raum`, nicht `polygon`** — und das ist kein Geschmack, sondern N1-07.
    // Mein erster Entwurf nahm `polygon`, weil die Datei da ist und das Bild passt. Die Zusage
    // zaehlt aber die ZEICHENKETTE `'polygon'` in dieser Datei und kann ein Bild nicht von einer
    // Werkzeug-id unterscheiden — sie ging sofort rot. *Die Zusage hat recht behalten, auch wenn
    // meine Absicht harmlos war:* eine Datei, in der `'polygon'` steht, laesst sich nicht mehr
    // per Blick von einer Wiederbelebung unterscheiden. `raum` ist ausserdem das ehrlichere Bild —
    // eine Kontur ist der Umriss einer Flaeche. Die sechs Primitive bleiben stillgelegt.
    //
    // **Kein `bauteilKind`:** eine Kontur ist kein Bauteil. Sie wird zu einem, wenn Z-06 sie zur
    // Decke macht — dort gehoert die Zuordnung hin.
    id: 'kontur',
    label: 'Kontur',
    icon: 'raum',
    art: 'werkzeug',
    groupId: 'gebaeude',
    supportedWorkspaces: [WORKSPACE_ARCHITEKTUR],
    supportedViews: ['2d', 'split'],
    shortcut: 'U',
    helpText: 'Mehrpunkt-Umriss zeichnen (L-, T-, U-Form) — Klick auf den ersten Punkt oder Enter schliesst.',
    meaning: 'Freien Umriss aus mehreren Punkten aufnehmen.',
    usageArea: 'Architektur — Grundlage fuer Decke und Dach.',
    group: 'Architektur',
    tooltip: {
      title: 'Kontur',
      body: 'Mehrpunkt-Umriss zeichnen. Klick auf den ersten Punkt oder Enter schliesst, Esc verwirft.',
      usage: 'Einsatzbereich: Flaechen, die keine Rechtecke sind — L-, T- und U-Formen.',
    },
  },
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
  {
    // A-35 — das ERSTE Werkzeug nach dem Bedienmodell A7 (Yama, 13.08.). Bis hierher galten acht
    // Werkzeuge als nicht baubar, weil ein Bedienmuster fehlte; es fehlte nicht, es war nur nie
    // benutzt worden. `minSelectionCount: 2`, weil ohne zweite Wand keine Schnittkante existiert.
    id: 'trimmen',
    label: 'Trimmen',
    icon: 'trimmen',
    art: 'aktion',
    groupId: 'global',
    supportedWorkspaces: [WORKSPACE_ARCHITEKTUR],
    supportedViews: [],
    minSelectionCount: 2,
    requiredPermissions: ['Hausplaner,update'],
    supportedSelectionTypes: ['wall'],
    helpText: 'Wand an einer anderen abschneiden: Schnittkanten zuerst auswählen, die zu kürzende Wand zuletzt.',
    activationRules: [
      { type: 'object-state', operator: 'not-equals', value: 'locked', grund: 'Eine gesperrte Wand kann nicht gekürzt werden.' },
      { type: 'project-state', operator: 'not-equals', value: 'readonly', grund: 'Der Plan ist schreibgeschützt.' },
    ],
    // Bedeutung, Einsatz, Kategorie und Icon kommen WOERTLICH aus dem Paket (werkzeugPaket.ts:123) —
    // `gehobeneWerkzeuge.test.ts` vergleicht sie Feld fuer Feld, damit Handeintrag und Paket nicht
    // auseinanderlaufen. Der helpText darueber ist die BEDIENANWEISUNG und wird nicht verglichen.
    meaning: 'Überstehende Linien an Begrenzungen abschneiden.',
    usageArea: 'Grundriss- und Detailkorrektur.',
    group: 'CAD',
    tooltip: { title: 'Trimmen', body: 'Überstehende Linien an Begrenzungen abschneiden.', usage: 'Einsatzbereich: Grundriss- und Detailkorrektur.' },
  },
];

const BY_ID = new Map(TOOL_DEFINITIONS.map((t) => [t.id, t]));
const BY_SHORTCUT = new Map(
  TOOL_DEFINITIONS.filter((t) => t.shortcut).map((t) => [t.shortcut!.toLowerCase(), t]),
);

/** Werkzeug nach id (oder undefined). */
/**
 * **Die Bilanz, getrennt statt hochgezaehlt** (Z-05-N1).
 *
 * Bis heute stand in acht Zusagen eine nackte `110`. Waere sie auf `111` gehoben worden, haette
 * die Zusage genau das verloren, wofuer es sie gibt: **dass ein VERSCHWUNDENES Paket-Werkzeug
 * auffaellt.** Faellt eines raus und kommt ein eigenes dazu, bleibt die Summe gleich — eine
 * nackte Zahl merkt das nicht, die getrennte Zaehlung schon.
 *
 * ```text
 * PAKET_WERKZEUGE  = 110          Konstante, benannt, unveraendert
 * EIGENE_WERKZEUGE = ['kontur']   Liste, waechst
 * erwartet         = PAKET_WERKZEUGE + EIGENE_WERKZEUGE.length
 * ```
 */
export const PAKET_WERKZEUGE = 110;

/**
 * **W-05 — die Hebeliste: REINE IDS, keine Definitionen.**
 *
 * `toolCatalog` importiert sie und laesst genau diese Werkzeuge weg; die Definitionen selbst
 * stehen oben in `GRUND_WERKZEUGE`. *Die Kante `toolCatalog -> toolRegistry` ist neu, aber
 * einseitig: diese Datei importiert `toolCatalog` nicht.*
 *
 * **Die Summe bleibt konstant, weil ein Werkzeug WANDERT statt sich zu verdoppeln** — dieselbe
 * Rechnung wie `PAKET + EIGENE.length` aus Z-05-N1: nicht die Zahl anfassen, sondern sie rechnen.
 *
 * **Warum `AUS_PAKET_GEHOBEN` und nicht `GEHOBEN`:** `GEHOBEN` ist vergeben (`schattenGehoben`,
 * `ROH_GEHOBEN` in `__tests__/elevationTokens.test.ts`). *Zwei Bedeutungen fuer einen Namen sind
 * eine zweite Wahrheit — gemessen, bevor der Name gewaehlt wurde.*
 */
export const AUS_PAKET_GEHOBEN = ['bemassen', 'flaeche-messen', 'trimmen'] as const;

/** Werkzeuge, die NACH dem 110er-Fachpaket dazugekommen sind. */
export const EIGENE_WERKZEUGE = ['kontur'] as const;

/** Die erwartete Gesamtzahl — eine Stelle, acht Leser. */
export const WERKZEUGE_GESAMT = PAKET_WERKZEUGE + EIGENE_WERKZEUGE.length;

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
