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
import { dachflaechen, dachMeshWelt } from './dachMesh';

/** Ein Dach, das die 3D nicht zeichnen kann — mit dem Grund im Klartext der Domäne. */
export type NichtDarstellbar = { nodeId: string; grund: string };

/**
 * Prüft jedes Dach, indem der Mesh-Bauplan **wirklich gebaut** wird.
 *
 * **Zwei Eingangsbedingungen** (A-01-4 + A-10-1): der Wurf `DachGeometrieUngueltig` — und das
 * *leere Ergebnis ohne Wurf* (null Flächen, wie `l-shape` ohne `anbau`-Maße). Beide enden in
 * derselben Meldung; ein Dach, das nichts zeigt, sagt es in jedem Fall.
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
      const mesh = dachMeshWelt(dach);
      // A-10-1 — die ZWEITE Eingangsbedingung: **das leere Ergebnis ohne Wurf.** Eine
      // `l-shape`-Kontur ohne `anbau`-Maße wirft nicht — sie liefert `{ dreiecke: [] }`, und
      // dieses Dach blieb bis A-10 stumm: nichts gezeichnet, nichts gemeldet.
      //
      // **Warum die Bedingung an den DREIECKEN hängt und nicht an `dachflaechen` allein**
      // (gemessen, 10.08.): ein `l-shape` MIT `anbau` liefert 10 Dreiecke und trotzdem
      // `dachflaechen() = []` — denn `dachflaechen` filtert auf rechteckige Trägerflächen
      // (walm ebenso). Wer `dachflaechen === 0` allein meldet, meldet zeichenbare Dächer.
      //
      // **Warum `dachflaechen` trotzdem mitgefragt wird:** „null Flächen" heißt, die
      // Dachberechnung liefert auf KEINEM ihrer beiden Ausgänge etwas — weder Dreiecke fürs
      // Bild noch Trägerflächen für Aufbauten. Beide lesen dieselbe Quelle (`dachRoh`), am
      // Nullpunkt sind sie heute gekoppelt (keine Dreiecke ⇒ keine Trägerflächen); die
      // Konjunktion ändert das Verhalten also nicht, sie hält die Übersetzung des Kriteriums
      // vollständig — und die A-10-5-Zusage hält fest, dass niemand sie still verengt.
      if (mesh.dreiecke.length === 0 && dachflaechen(dach).length === 0) {
        gefunden.push({
          nodeId: dach.id,
          grund: `die Berechnung der Dachform „${dach.roofType}" liefert keine einzige Fläche — es gibt nichts zu zeichnen`,
        });
      }
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
