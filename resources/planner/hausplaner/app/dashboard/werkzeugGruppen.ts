/**
 * I4 (AUF-21) — die 110 Werkzeuge als **Gruppen der oberen Werkzeugleiste**.
 *
 * Nach I2 lagen alle Fach-Werkzeuge als Daten im Katalog und **keines** war erreichbar. Dieses
 * Modul ordnet sie den 22 Paket-Kategorien zu — Yamas Freigabe auf die Frage, ob nach Kategorien
 * gruppiert werden darf, war „ja".
 *
 * **Warum ein Datenmodul und nicht Markup:** damit „22 Gruppen, Summe 110, jedes Werkzeug in genau
 * einer Gruppe" prüfbar ist. Ein Menü, das im JSX zusammengesetzt wird, kann man nur ansehen.
 *
 * **Die Quelle ist beides:** der Katalog (101 Paket-Werkzeuge) **und** die Registry (9 Bestands-
 * Werkzeuge, die seit AUF-31 die Kategorie des Pakets tragen). Ein Werkzeug taucht genau einmal auf;
 * die Registry hat Vorrang, falls eine id doppelt vorkäme — nach AUF-31 kann das nicht mehr passieren,
 * der Test hält es trotzdem fest.
 *
 * **Was hier NICHT entschieden wird:** ob ein Werkzeug bedienbar ist. Das sagt weiterhin
 * `resolveToolState` — eine Wahrheit, kein zweiter Mechanismus.
 */
import type { ToolDefinition } from '../tools/toolTypes';
import { TOOL_KATALOG } from '../tools/toolCatalog';
import { TOOL_DEFINITIONS } from '../tools/toolRegistry';

export interface WerkzeugGruppe {
  /** Schlüssel für Menü-State und Tests: ASCII, klein, ohne Umlaut. */
  id: string;
  /** Anzeigename — die Kategorie aus dem Paket, unverändert deutsch. */
  label: string;
  werkzeuge: readonly ToolDefinition[];
}

/** Kategorie → Menü-Schlüssel. Umlaute ausgeschrieben, wie bei den Werkzeug-IDs (AUF-31). */
function schluessel(kategorie: string): string {
  return kategorie
    .toLowerCase()
    .replace(/ä/g, 'ae').replace(/ö/g, 'oe').replace(/ü/g, 'ue').replace(/ß/g, 'ss')
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/^-|-$/g, '');
}

/**
 * Anzeigereihenfolge der Gruppen. Fest, nicht alphabetisch und nicht nach Größe — sie folgt dem
 * Arbeitsablauf: erst auswählen und bearbeiten, dann zeichnen und konstruieren, dann das Gebäude,
 * dann die Gewerke, zuletzt Ansicht und System. Eine Leiste, deren Reihenfolge sich mit den Daten
 * ändert, zwingt den Nutzer jedes Mal zum Suchen.
 */
const REIHENFOLGE = [
  'Auswahl', 'Bearbeiten', 'Zeichnen', 'CAD', 'Architektur', 'Fassade', 'Material',
  'Bauphysik', 'Heizung', 'TGA', 'Sanitär', 'Bad', 'Küche', 'Elektro', 'PV',
  'Messen', 'Prüfung', 'Import', 'Workflow', 'Zusammenarbeit', 'Ansicht', 'System',
] as const;

/** Die Kategorie eines Werkzeugs — Katalog führt sie in `groupId`, Registry in `group`. */
function kategorieVon(t: ToolDefinition): string {
  return t.group ?? t.groupId;
}

export const WERKZEUG_GRUPPEN: readonly WerkzeugGruppe[] = ((): WerkzeugGruppe[] => {
  const nachKategorie = new Map<string, ToolDefinition[]>();
  const gesehen = new Set<string>();
  // Registry zuerst: bei Namensgleichheit gewinnt der Bestand (wie in `zoneTools`).
  for (const t of [...TOOL_DEFINITIONS, ...TOOL_KATALOG]) {
    if (gesehen.has(t.id)) continue;
    gesehen.add(t.id);
    const k = kategorieVon(t);
    nachKategorie.set(k, [...(nachKategorie.get(k) ?? []), t]);
  }
  const geordnet = [
    ...REIHENFOLGE.filter((k) => nachKategorie.has(k)),
    // Eine Kategorie, die das Paket später ergänzt, fällt nicht heraus — sie hängt sich hinten an,
    // statt still zu verschwinden.
    ...[...nachKategorie.keys()].filter((k) => !REIHENFOLGE.includes(k as typeof REIHENFOLGE[number])),
  ];
  return geordnet.map((k) => ({ id: schluessel(k), label: k, werkzeuge: nachKategorie.get(k) ?? [] }));
})();

/** Eine Gruppe nach Schlüssel. */
export function gruppeNach(id: string): WerkzeugGruppe | undefined {
  return WERKZEUG_GRUPPEN.find((g) => g.id === id);
}

/** Die Gruppe, in der ein Werkzeug steht (oder undefined). */
export function gruppeVonWerkzeug(toolId: string): WerkzeugGruppe | undefined {
  return WERKZEUG_GRUPPEN.find((g) => g.werkzeuge.some((t) => t.id === toolId));
}

/** Werkzeuge, die in mehr als einer Gruppe stehen — muss leer sein. */
export function mehrfachGefuehrt(): string[] {
  const zaehler = new Map<string, number>();
  for (const g of WERKZEUG_GRUPPEN) {
    for (const t of g.werkzeuge) zaehler.set(t.id, (zaehler.get(t.id) ?? 0) + 1);
  }
  return [...zaehler].filter(([, n]) => n > 1).map(([id]) => id);
}

/**
 * Der Icon-Pfad eines Werkzeugs — **aus der id abgeleitet, nicht aus dem `icon`-Feld.**
 *
 * Grund, am Code gemessen: das Paket führt `icon: 'icons/<id>.svg'`, die Dateien liegen aber seit I1
 * unter `public/hausplaner/icons/tools/`. Das Feld übernehmen hieße 101 Bilder mit 404. Seit AUF-31
 * heißt jede Datei wie ihre deutsche id — damit ist die id die verlässlichere Quelle. Das `icon`-Feld
 * bleibt unangetastet (kein Beifang), wird hier aber bewusst nicht gelesen.
 */
export function iconPfad(t: ToolDefinition): string {
  return `/hausplaner/icons/tools/${t.id}.svg`;
}
