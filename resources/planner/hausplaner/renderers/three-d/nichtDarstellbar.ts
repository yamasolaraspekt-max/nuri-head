/**
 * A-01-4 — **Welche Dächer kann die 3D nicht zeichnen?**
 *
 * ---
 *
 * **Der Befund, der diese Datei erzwungen hat:** ein Bestandsdokument mit L-Kontur zeigte in der
 * 3D eine *leere Stelle*. Kein Fehler, kein Hinweis, kein Dach — nur nichts. Zwei Fänger in
 * `szene.ts` schluckten den `DachGeometrieUngueltig`-Wurf und fuhren weiter.
 *
 * **Warum die Entscheidung hier steht und nicht in den Fängern:** ein Fänger im Renderer ist
 * nicht prüfbar, ohne einen WebGL-Kontext zu bauen. Was nicht prüfbar ist, wird still wieder
 * kaputt — *genau so ist der geschluckte Wurf entstanden.* Diese Funktion trifft dieselbe
 * Entscheidung mit denselben Mitteln (`dachMeshWelt`), ohne `three` zu berühren, und ist
 * deshalb in einem gewöhnlichen Node-Test zu fahren.
 *
 * Dieselbe Trennung wie bei `commit-pruefen.sh` und `browser-buehne.sh`: **die
 * Entscheidungsfunktion ist prüfbar, der Ausführer nicht — also entscheidet nicht der Ausführer.**
 *
 * Die Fänger in `szene.ts` bleiben bestehen, denn der Wurf muss abgefangen werden. Sie **melden
 * aber nicht mehr selbst** — sonst gäbe es zwei Orte, die dieselbe Frage beantworten, und der
 * eine wäre prüfbar und der andere nicht.
 */
import type { RoofNode } from '../../domain/scene.types';
import { DachGeometrieUngueltig } from '../../geometry/dachGeometrie';
import { dachMeshWelt } from './dachMesh';

/** Ein Dach, das die 3D nicht zeichnen kann — mit dem Grund im Klartext der Domäne. */
export type NichtDarstellbar = { nodeId: string; grund: string };

/**
 * Prüft jedes Dach, indem der Mesh-Bauplan **wirklich gebaut** wird.
 *
 * *Keine nachgebaute Bedingung.* Eine zweite Prüfung der Art „hat die Kontur vier Punkte?" wäre
 * eine zweite Wahrheit — sie könnte irgendwann anders urteilen als der Renderer, und dann meldet
 * die Oberfläche ein Dach als darstellbar, das leer bleibt. Deshalb dieselbe Frage an dieselbe
 * Stelle.
 */
export function nichtDarstellbareDaecher(daecher: readonly RoofNode[]): NichtDarstellbar[] {
  const gefunden: NichtDarstellbar[] = [];

  for (const dach of daecher) {
    try {
      dachMeshWelt(dach);
    } catch (fehler) {
      if (fehler instanceof DachGeometrieUngueltig) {
        gefunden.push({ nodeId: dach.id, grund: fehler.message });
        continue;
      }
      // Alles andere ist ein echter Fehler und gehört nicht verschluckt — der Unterschied
      // zwischen „kann ich nicht zeichnen" und „hier ist etwas kaputt" muss sichtbar bleiben.
      throw fehler;
    }
  }

  return gefunden;
}
