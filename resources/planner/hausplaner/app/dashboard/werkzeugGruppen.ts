/**
 * I4 (AUF-21) — die 110 Werkzeuge als **Gruppen der oberen Werkzeugleiste**.
 * AUF-34 / Nachtrag 2 — die Gruppen sind seit Yamas Entscheidung „15" die **15 Themen** des
 * Funktionsvertrag-Pakets, nicht mehr die 22 Kategorien.
 *
 * **Warum gewechselt wurde, gemessen:** 22 gleichrangige Menüs liefen bei 1440 px über drei Zeilen,
 * und zwei davon (`TGA`, `Sanitär`) trugen **je ein** Werkzeug. Die Themen fassen genau das
 * zusammen und sind eine vollständige Zerlegung (Summe exakt 110, jedes Werkzeug in genau einem).
 *
 * **Die Kategorie ist nicht verschwunden** — sie steht weiter an jedem Werkzeug (`group`/`groupId`)
 * als Trail. Sie gruppiert nur nichts mehr. Zwei Gruppierungen nebeneinander wären eine zweite
 * Wahrheit; deshalb gibt es hier genau eine.
 *
 * **Warum ein Datenmodul und nicht Markup:** damit „15 Gruppen, Summe 110, jedes Werkzeug in genau
 * einer Gruppe" prüfbar ist. Ein Menü, das im JSX zusammengesetzt wird, kann man nur ansehen.
 *
 * **Was hier NICHT entschieden wird:** ob ein Werkzeug bedienbar ist. Das sagt weiterhin
 * `resolveToolState` — eine Wahrheit, kein zweiter Mechanismus.
 */
import type { ToolDefinition } from '../tools/toolTypes';
import { TOOL_KATALOG } from '../tools/toolCatalog';
import { TOOL_DEFINITIONS } from '../tools/toolRegistry';
import { WERKZEUG_THEMEN, kurzLabel } from '../tools/werkzeugThemen';
import { themenFuer } from './arbeitsbereiche';

export interface WerkzeugGruppe {
  /** Schlüssel für Menü-State und Tests = die technische Themen-id (`07-architektur`). */
  id: string;
  /** Anzeigename — das deutsche Themen-Label, unverändert aus dem Paket. */
  label: string;
  /** Kurzform für die Leiste (erster Begriff); das volle Label bleibt im `title`. */
  kurz: string;
  werkzeuge: readonly ToolDefinition[];
}

/**
 * Alle Werkzeuge nach id. **Die Registry hat Vorrang** — bei Namensgleichheit gewinnt der Bestand
 * (wie in `zoneTools`). Seit AUF-31 kann eine id nicht mehr doppelt vorkommen; der Vorrang bleibt
 * als Regel stehen, damit ein künftiger Paket-Import den Bestand nicht still überschreibt.
 */
const NACH_ID = ((): Map<string, ToolDefinition> => {
  const m = new Map<string, ToolDefinition>();
  for (const t of [...TOOL_DEFINITIONS, ...TOOL_KATALOG]) if (!m.has(t.id)) m.set(t.id, t);
  return m;
})();

export const WERKZEUG_GRUPPEN: readonly WerkzeugGruppe[] = WERKZEUG_THEMEN.map((t) => ({
  id: t.id,
  label: t.label,
  kurz: kurzLabel(t),
  // Ein Thema, das eine unbekannte id nennt, verliert sie hier still — `werkzeugeOhneGruppe()`
  // macht genau das sichtbar, statt es im Menü zu verstecken.
  werkzeuge: t.werkzeuge.map((id) => NACH_ID.get(id)).filter((x): x is ToolDefinition => Boolean(x)),
}));

/**
 * Die Gruppen **eines Arbeitsbereichs** — durchgängige und gebundene, in Themen-Reihenfolge.
 * Das ist die Liste, die die Leiste rendert; `WERKZEUG_GRUPPEN` bleibt die Gesamtsicht für Tests
 * und für die Bilanz „nichts verloren".
 */
export function gruppenFuer(bereichId: string): readonly WerkzeugGruppe[] {
  const erlaubt = new Set(themenFuer(bereichId).map((t) => t.id));
  return WERKZEUG_GRUPPEN.filter((g) => erlaubt.has(g.id));
}

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

/** Werkzeuge aus Registry oder Katalog, die in keinem Thema stehen — muss leer sein, sonst unerreichbar. */
export function werkzeugeOhneGruppe(): string[] {
  const drin = new Set(WERKZEUG_GRUPPEN.flatMap((g) => g.werkzeuge.map((t) => t.id)));
  return [...NACH_ID.keys()].filter((id) => !drin.has(id));
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
