/**
 * A-35 — **Trimmen: das erste Werkzeug nach dem Bedienmodell A7.**
 *
 * ---
 *
 * **Was es tut.** Eine Wand wird an einer anderen abgeschnitten. Die Mathematik dafür liegt seit
 * A-32 (`geradenSchnitt`, F-004); was fehlte, war ein Werkzeug, das sie bedienbar macht.
 *
 * **Die Rollen kommen aus der vorhandenen Mehrfachauswahl** (`ANFORDERUNGEN.md` A7, von Yama
 * bestätigt 13.08.):
 *
 * ```text
 * alle vorgewaehlten Objekte   ->  SCHNITTKANTEN   (Nebenrolle)
 * das ZULETZT angeklickte      ->  das zu kuerzende Objekt (Hauptrolle) = primaerId
 * ```
 *
 * *Das ist zugleich das CAD-Standardmuster: Schnittkanten vorwählen, dann das zu kürzende Objekt
 * klicken.*
 *
 * **Diese Datei fasst den Store nicht an.** Sie bekommt Knoten und gibt **Befehle** zurück — dasselbe
 * Muster wie `sammelBefehle` (A-31). Eine Zusage kann sie aufrufen, das Ergebnis gegen den Store
 * fahren und **rot werden**.
 *
 * ---
 *
 * ## Warum jede Absage einen Grund trägt
 *
 * **Ein Werkzeug, das bei einer unmöglichen Lage einfach nichts tut, sieht aus wie ein defektes
 * Werkzeug.** Der Anwender klickt, nichts passiert, und er weiß nicht, ob er falsch ausgewählt hat
 * oder ob die Anwendung hängt. *Deshalb gibt es hier keinen stillen Rückweg:* **jeder Fall, der
 * nicht trimmt, liefert einen benannten Grund** — wie bei der Konturprüfung (W-18).
 */
import type { HausplanerCommand } from '../../domain/commands.types';
import type { SceneNode, WallNode } from '../../domain/scene.types';
import { geradenSchnittParameter } from '../../geometry/geradenGeometrie';

/** Warum ein Trimmvorgang nicht ausgeführt wurde. */
export type TrimmGrund =
  | 'kein_ziel'
  | 'ziel_gesperrt'
  | 'keine_schnittkante'
  | 'parallel'
  | 'ausserhalb'
  | 'mehrdeutig';

/** Was der Anwender liest. Jeder Satz nennt den Grund UND den nächsten Handgriff. */
export const TRIMM_MELDUNG: Record<TrimmGrund, string> = {
  kein_ziel: 'Zum Trimmen zuletzt die Wand anklicken, die gekürzt werden soll.',
  ziel_gesperrt: 'Diese Wand ist gesperrt — entsperre sie, dann lässt sie sich kürzen.',
  keine_schnittkante: 'Trimmen braucht mindestens eine zweite Wand als Schnittkante — wähle sie zuerst aus.',
  parallel: 'Die Wände laufen parallel — sie schneiden sich nirgends.',
  ausserhalb: 'Die Wände kreuzen sich erst außerhalb ihrer Enden — verlängere sie zuerst.',
  mehrdeutig: 'Zwei Schnittkanten würden gleich viel wegnehmen — wähle eine von beiden ab.',
};

export interface TrimmErgebnis {
  /** Leer, wenn nicht getrimmt wurde. Sonst genau EIN `MOVE_NODE`. */
  befehle: HausplanerCommand[];
  /** `null`, wenn getrimmt wurde. */
  grund: TrimmGrund | null;
}

interface Kandidat {
  /** Der Schnittpunkt, **auf ganze Millimeter gerundet** — die Lage, die wirklich gebaut würde. */
  punkt: { x: number; y: number };
  /** `true`, wenn `end` zum Schnittpunkt wandert; sonst `start`. */
  endeWandert: boolean;
  /**
   * Wie viel Wand wegfiele — als **quadrierter** Abstand in mm².
   *
   * *Quadriert, weil `start`, `end` und der gerundete Schnittpunkt allesamt ganzzahlig sind:*
   * `dx² + dy²` **ist damit eine ganze Zahl und exakt vergleichbar.** Die Wurzel wäre es nicht, und
   * genau daran wäre die Gleichstands-Erkennung gescheitert (s. Kopf der Datei).
   */
  verlustQ: number;
}

/** Quadrierter Abstand zweier ganzzahliger Punkte — ohne Wurzel, damit der Vergleich exakt bleibt. */
function abstandQ(a: { x: number; y: number }, b: { x: number; y: number }): number {
  const dx = a.x - b.x;
  const dy = a.y - b.y;
  return dx * dx + dy * dy;
}

function istWand(n: SceneNode | undefined): n is WallNode {
  return n !== undefined && n.type === 'wall';
}

/**
 * **Die Befehlsliste eines Trimmvorgangs — oder der Grund, warum es keine gibt.**
 *
 * Erwartet die Auswahl **in Klickreihenfolge** und `primaerId` als das zuletzt angeklickte Objekt.
 * Beides liefert `wendeAuswahlAn` (`auswahlModus.ts`) bereits so; hier wird nichts umsortiert.
 *
 * ---
 *
 * ## Die zwei Entscheidungen, die dieser Auftrag mir überlassen hat — benannt, nicht still
 *
 * ### 1 · Welches Ende wandert, und welche Schnittkante gewinnt: **die kleinste Kürzung**
 *
 * *Im CAD zeigt der Klickpunkt, welches Stück weg soll.* **Den gibt es hier nicht:** `waehleAn`
 * (`HausplanerApp.tsx:815`) bekommt `(id, MouseEvent)` — eine Kennung und ein Ereignis, **keine
 * Weltkoordinate.** Der in K6 vorgeschlagene Operand existiert im Modell nicht.
 *
 * > ***Deshalb gilt eine Regel für beides: es wird so wenig gekürzt wie möglich.*** *Von den beiden
 * > Enden wandert das, das dem Schnittpunkt näher liegt; von mehreren Schnittkanten gewinnt die,
 * > die am wenigsten wegnimmt.*
 *
 * **Warum das der richtige Standard ist:** *ein zu kurz geratener Schnitt kostet ein Undo, ein zu
 * langer kostet die halbe Wand.* **Und die Regel ist von der Auswahlreihenfolge unabhängig** — K6
 * verbietet ausdrücklich „die erste in der Liste", weil das Ergebnis sonst davon abhinge, in welcher
 * Reihenfolge jemand geklickt hat.
 *
 * ### 2 · Wenn beide Enden gleich weit weg sind: **`end` wandert**
 *
 * *Bei `t = 0,5` sagt „kleinste Kürzung" nichts mehr — beide Hälften sind gleich lang.* **Dann
 * entscheidet die Richtung der Wand selbst:** `start → end` ist die Zeichenrichtung, und `end` ist
 * das Ende, zu dem der Anwender zuletzt gezogen hat. *Dasselbe Prinzip, auf dem das ganze
 * A7-Modell steht: das Zuletzt-Bearbeitete ist das Gemeinte.*
 *
 * **Zwei VERSCHIEDENE Schnittkanten mit gleichem Verlust sind etwas anderes** — dort liegen die
 * Schnittpunkte auseinander, das Ergebnis wäre zwei verschiedene Wände. *Das wird abgewiesen
 * (`mehrdeutig`) und nicht geraten.*
 *
 * ### ⚠ Und „gleich viel" muss EXAKT vergleichbar sein — die erste Fassung war es nicht
 *
 * *Zuerst verglich ich den **Anteil** `min(t, 1−t)`.* **Gemessen an zwei symmetrischen Kanten
 * (x = 2000 und x = 8000 auf einer 10-m-Wand):**
 *
 * ```text
 * min(0.2, 1-0.2)  =  0.2
 * min(0.8, 1-0.8)  =  0.19999999999999996      <- 1-0.8 ist in Gleitkomma nicht 0.2
 * ```
 *
 * > ***Der Gleichstand wäre nie erkannt worden.*** *Das Werkzeug hätte die rechte Kante genommen —
 * > weil `1-0.8` einen Hauch kleiner ist, nicht weil die Geometrie es sagt.* **Genau das Raten, das
 * > K6 verbietet, nur unsichtbar.**
 *
 * **Deshalb wird jetzt der QUADRIERTE Abstand zum gerundeten Schnittpunkt verglichen.** *Wandenden
 * und gerundeter Schnittpunkt sind ganze Millimeter, also ist `dx² + dy²` eine **ganze Zahl** —
 * exakt vergleichbar, keine Wurzel, kein Epsilon.* **Und der Vergleich läuft damit auf der Lage,
 * die wirklich gebaut würde**, nicht auf einem Zwischenwert davor.
 */
export function befehleTrimmen(
  auswahl: { ids: readonly string[]; primaerId: string | null },
  nodes: readonly SceneNode[],
): TrimmErgebnis {
  const ziel = nodes.find((n) => n.id === auswahl.primaerId);
  if (!istWand(ziel)) {
    return { befehle: [], grund: 'kein_ziel' };
  }
  if (ziel.locked) {
    return { befehle: [], grund: 'ziel_gesperrt' };
  }

  // K4: das zu kuerzende Objekt darf laut `auswahlModus` Fall `add` auch in `ids` stehen. Es ist
  // dann NICHT seine eigene Schnittkante — eine Gerade schneidet sich nicht selbst.
  const kanten = auswahl.ids
    .filter((id) => id !== ziel.id)
    .map((id) => nodes.find((n) => n.id === id))
    .filter(istWand);

  if (kanten.length === 0) {
    return { befehle: [], grund: 'keine_schnittkante' };
  }

  const kandidaten: Kandidat[] = [];
  let hatSchnitt = false; // trennt „parallel" von „kreuzt sich erst ausserhalb"

  for (const kante of kanten) {
    const lage = geradenSchnittParameter(ziel.start, ziel.end, kante.start, kante.end);
    if (lage === null) {
      continue; // parallel, deckungsgleich oder eine Achse ohne Länge
    }
    hatSchnitt = true;

    // K3, ueber die Parameter und NICHT ueber die Koordinate: beide dimensionslos, exakt
    // vergleichbar, kein Abstands-Epsilon noetig. `u` haelt fest, dass der Punkt auch auf der
    // SCHNITTKANTE liegt — eine Kante, die erst hinter ihrem eigenen Ende trifft, schneidet nicht.
    if (lage.t < 0 || lage.t > 1 || lage.u < 0 || lage.u > 1) {
      continue;
    }

    // mm-Invariante ZUERST: der Reducer nimmt nur ganze Millimeter (`pruefeGanzzahlig`), also wird
    // hier gerundet — und der Vergleich laeuft danach auf der Lage, die WIRKLICH gebaut wuerde.
    const punkt = { x: Math.round(lage.punkt.x), y: Math.round(lage.punkt.y) };
    const vonStart = abstandQ(ziel.start, punkt);
    const vonEnde = abstandQ(ziel.end, punkt);
    kandidaten.push({
      punkt,
      endeWandert: vonEnde <= vonStart, // Gleichstand (Mitte) -> `end`, s. Kopf
      verlustQ: Math.min(vonStart, vonEnde),
    });
  }

  if (kandidaten.length === 0) {
    return { befehle: [], grund: hatSchnitt ? 'ausserhalb' : 'parallel' };
  }

  let beste = kandidaten[0];
  let gleichstand = false;
  for (const k of kandidaten.slice(1)) {
    if (k.verlustQ < beste.verlustQ) {
      beste = k;
      gleichstand = false;
    } else if (k.verlustQ === beste.verlustQ && (k.punkt.x !== beste.punkt.x || k.punkt.y !== beste.punkt.y)) {
      // Gleich viel Verlust, aber an einer ANDEREN Stelle — zwei verschiedene Ergebnisse.
      gleichstand = true;
    }
  }
  if (gleichstand) {
    return { befehle: [], grund: 'mehrdeutig' };
  }

  const s = beste.punkt;
  const position = beste.endeWandert
    ? { start: { ...ziel.start }, end: s }
    : { start: s, end: { ...ziel.end } };

  return {
    befehle: [{ type: 'MOVE_NODE', nodeId: ziel.id, position }],
    grund: null,
  };
}
