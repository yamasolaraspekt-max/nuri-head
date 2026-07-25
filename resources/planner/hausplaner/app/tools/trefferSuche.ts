/**
 * AUF-35a — der **Hit-Test** als reine Funktion.
 *
 * **Was er beantwortet:** Welches Objekt hat der Nutzer getroffen, wenn mehrere übereinanderliegen?
 * Ohne diese Antwort gewinnt in einem Canvas immer das zuletzt gezeichnete oder das zufällig
 * zuerst gefundene — beides ist keine Regel, sondern ein Nebeneffekt.
 *
 * **Die Regel aus Yamas Referenz, übernommen:** Treffer werden **erst nach Zeichenreihenfolge,
 * dann nach Distanz** sortiert. Was oben liegt, gewinnt; liegen zwei gleich hoch, gewinnt das
 * nähere. Unsichtbare und nicht wählbare Objekte fallen vorher heraus.
 *
 * **Warum nicht Konvas eigener Hit-Test:** Der arbeitet auf gezeichneten Formen und ist damit an
 * den 2D-Renderer gebunden. Die Auswahl gilt aber für **beide** Ansichten (eine Auswahl, 2D und
 * 3D). Eine reine Funktion über Kandidaten ist in beiden benutzbar und ohne Browser testbar.
 *
 * **Kante 1 des Auftrags:** Ein **gesperrtes** Objekt bleibt wählbar. Es aus dem Hit-Test zu
 * werfen wäre falsch — man muss sehen können, was sperrt. Ausgeschlossen wird nur, was
 * **unsichtbar** oder ausdrücklich **nicht wählbar** ist.
 */

export interface TrefferKandidat {
  id: string;
  /** Abstand des Klickpunkts zum Objekt, in Weltmaß (mm). 0 = innerhalb. */
  distanz: number;
  /**
   * Zeichenreihenfolge: höher = weiter oben. Entspricht der Reihenfolge, in der der Renderer
   * zeichnet — das ist die Reihenfolge, die der Nutzer sieht.
   */
  zeichenreihenfolge: number;
  sichtbar: boolean;
  /** Gesperrt heißt **nicht** unwählbar (Kante 1) — es steht hier nur zur Vollständigkeit. */
  gesperrt?: boolean;
  /** Ausdrücklich nicht wählbar (z. B. Hilfslinien, Raster, Bemaßung). */
  waehlbar?: boolean;
}

/**
 * Der beste Treffer innerhalb der Toleranz — oder `null`.
 *
 * `toleranz` kommt in **Weltmaß** herein, nicht in Pixeln: der Aufrufer rechnet den Pixel-Radius
 * über den Zoom um. So bleibt diese Funktion unabhängig vom Maßstab, und die Toleranz bedeutet
 * bei jedem Zoom dasselbe für den Nutzer (gleicher Abstand auf dem Bildschirm).
 */
export function besterTreffer(kandidaten: readonly TrefferKandidat[], toleranz: number): TrefferKandidat | null {
  const sortiert = trefferInReihenfolge(kandidaten, toleranz);
  return sortiert.length > 0 ? sortiert[0] : null;
}

/**
 * Alle Treffer innerhalb der Toleranz, **in Gewinner-Reihenfolge**: oben vor unten, bei
 * Gleichstand nah vor fern. Öffentlich, weil ein späterer „durchklicken"-Modus (mehrfach an
 * dieselbe Stelle klicken, um in die Tiefe zu wählen) genau diese Liste braucht — und weil sich
 * die Sortierung so einzeln prüfen lässt.
 */
export function trefferInReihenfolge(kandidaten: readonly TrefferKandidat[], toleranz: number): TrefferKandidat[] {
  return kandidaten
    .filter((k) => k.sichtbar)
    .filter((k) => k.waehlbar !== false)
    .filter((k) => k.distanz <= toleranz)
    .slice()
    .sort((a, b) => (
      // erst Zeichenreihenfolge (absteigend: was oben liegt, gewinnt) …
      b.zeichenreihenfolge - a.zeichenreihenfolge
      // … dann Distanz (aufsteigend: das nähere gewinnt)
      || a.distanz - b.distanz
    ));
}

/**
 * Pixel-Toleranz in Weltmaß umrechnen. `zoom` ist px pro mm (wie im Planer);
 * ein Zoom von 0 oder kleiner liefert die Toleranz unverändert, statt durch null zu teilen.
 */
export function toleranzInWelt(pixel: number, zoom: number): number {
  return zoom > 0 ? pixel / zoom : pixel;
}
