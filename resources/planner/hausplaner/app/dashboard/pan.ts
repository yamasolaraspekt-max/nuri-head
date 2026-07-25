/**
 * AUF-51 — der **Verschub der Zeichenfläche** (Pan) als Zustand.
 *
 * **Der gemessene Widerspruch:** Die Bühne war `draggable`, hatte aber **keinen einzigen
 * Drag-Handler**, und ihre Position stand als **gesteuerter Wert ohne Zustand dahinter**
 * (`x={80}`, `y={hoehe - 80}`). Zugleich rendert `onMouseMove → setCursor(...)` bei **jeder**
 * Mausbewegung neu — und jedes Rendern setzte die Bühne auf `x=80` zurück. Der Nutzer schob, und
 * es sprang zurück.
 *
 * Schlimmer als das Springen war die zweite Hälfte: `weltPunkt` liest `stage.x()`, also die
 * **echte** Position. Für die Dauer des Zurückspringens widersprachen sich Anzeige und Koordinate —
 * ein Klick landete nicht dort, wo der Nutzer hinzeigte. Das ist kein Layout-Mangel, das ist ein
 * Richtigkeitsfehler.
 *
 * **Die Wahl war „Pan-Zustand einführen oder `draggable` entfernen".** Eingeführt — weil
 * `weltPunkt` die verschobene Bühne bereits korrekt liest: der Rest der Anwendung ist auf einen
 * echten Verschub vorbereitet, es fehlte nur die Stelle, die ihn behält.
 *
 * **Warum `null` als Startwert und nicht `{x: 80, y: hoehe - 80}`:** Die Standardlage hängt von der
 * Fensterhöhe ab und soll ihr **weiter folgen**, solange der Nutzer nicht selbst verschoben hat.
 * Ein sofort gesetzter Absolutwert fröre die Lage beim ersten Rendern ein; nach einer
 * Fenstergrößen-Änderung stünde die Zeichnung schief, ohne dass jemand etwas getan hätte.
 */

/** Waagerechter Rand der Standardlage, px. Unverändert der Wert, der vorher fest im JSX stand. */
export const STANDARD_PAN_X = 80;
/** Senkrechter Rand der Standardlage, px — von unten gemessen, weil die Welt nach oben wächst. */
export const STANDARD_PAN_RAND = 80;

export interface Pan {
  x: number;
  y: number;
}

/** Die Standardlage zur aktuellen Bühnenhöhe. */
export function standardPan(hoehe: number): Pan {
  return { x: STANDARD_PAN_X, y: hoehe - STANDARD_PAN_RAND };
}

/**
 * Die tatsächliche Lage: der selbst verschobene Wert, sonst die Standardlage.
 * `null` heißt **„nie verschoben"** — dann folgt die Bühne der Fensterhöhe.
 */
export function panAus(pan: Pan | null, hoehe: number): Pan {
  return pan ?? standardPan(hoehe);
}

/** Hat der Nutzer selbst verschoben? Für Anzeige und für „Ansicht zurücksetzen". */
export function istVerschoben(pan: Pan | null): boolean {
  return pan !== null;
}
