/**
 * Hausplaner — Werkzeug-Katalog. REINE Daten.
 *
 * **Seit I2 (AUF-21, 25.07.2026) speist ihn Yamas 110er-Fachpaket** (`werkzeugPaket.ts`), abgebildet
 * über `paketAdapter.ts` auf `ToolDefinition`. Vorher standen hier 54 aus einem InDesign-Paket
 * abgeleitete Einträge, von denen **47 reine DTP-Reste** waren — Seitenwerkzeug, Textwerkzeug,
 * Pipette, Bézier, Rahmen, Farbfelder, Preflight. Sie sind **stillgelegt, nicht gelöscht**:
 * `toolCatalogStillgelegt.ts` hält sie als Trail; ein Drop wäre ein eigener Posten.
 *
 * Was der Katalog ist und was nicht: er sagt, **welche Werkzeuge es gibt** und wie sie heißen.
 * Er sagt **nicht**, wo sie erscheinen (`toolPresentation.ts`) und **nicht**, ob sie gerade
 * bedienbar sind (`activation.resolveToolState`). Diese Datei filtert nicht.
 *
 * Icons: `public/hausplaner/icons/tools/<id>.svg` (I1, `7bbf9ff`) — der Pfad steht je Eintrag im
 * Feld `icon` und stammt aus dem Paket.
 */
import type { ToolDefinition } from './toolTypes';
import { PAKET_ALS_TOOLS } from './paketAdapter';
import { AUS_PAKET_GEHOBEN } from './toolRegistry';

/**
 * Die Fach-Werkzeuge in Paket-Reihenfolge — **ohne die, die in die Leiste gehoben sind.**
 *
 * W-05: gefiltert wird HIER, nicht an der Quelle. `paketAdapter` liefert das Paket weiterhin
 * vollstaendig — sonst verloere `verworfeneKuerzel()` seine Grundgesamtheit und meldete ploetzlich
 * weniger Konflikte, weil ein Werkzeug fehlt statt weil es keinen Konflikt gibt.
 *
 * **Die Bilanz bleibt konstant:** was hier herausfaellt, steht in `TOOL_DEFINITIONS`.
 * *Ein Werkzeug wandert, es verdoppelt sich nicht.*
 */
export const TOOL_KATALOG: readonly ToolDefinition[] = PAKET_ALS_TOOLS.filter(
  (t) => !(AUS_PAKET_GEHOBEN as readonly string[]).includes(t.id),
);

export function katalogTool(id: string): ToolDefinition | undefined {
  return TOOL_KATALOG.find((t) => t.id === id);
}
