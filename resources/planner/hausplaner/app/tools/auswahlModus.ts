/**
 * AUF-35a — „Markieren": die **Auswahlmodi** als reine Logik.
 *
 * **Der gemessene Mangel:** Das Datenmodell kann seit jeher mehrere Objekte halten
 * (`selectedNodeIds: string[]`), die Oberfläche konnte genau eines — an fünf Stellen stand
 * `selectedNodeIds.length === 1`. Und die Modifikatortasten wurden beim Klick **gar nicht**
 * gelesen; `shiftKey`/`ctrlKey` kamen nur in der Tastaturbehandlung für Undo/Redo/Speichern vor.
 *
 * **Herkunft:** Yamas Referenz-Implementierung „Markieren / Auswahl" vom 25.07. Sie ist für Vue 3
 * und Pinia geschrieben; übernommen wird deshalb **die Logik, nicht der Rahmen** (Konflikt-Regel:
 * der neue Code passt sich dem Bestand an). Kein zweiter Store — der Auswahlzustand bleibt in
 * `hausplanerStore.ts`.
 *
 * **Warum reine Funktionen:** Ein Auswahlmodus, der im Klick-Handler entsteht, ist nur mit Browser
 * prüfbar. Hier ist er eine Abbildung `Eingabe → Modus` und `(vorher, Treffer, Modus) → nachher` —
 * ohne DOM, ohne Store, vollständig testbar.
 *
 * **Auswahl ändert das Modell nicht ⇒ kein Undo.** Deckt sich mit `undoable: false` im
 * Funktionsvertrag (AUF-36) und mit der Regel, dass nur `applyCommand` das Dokument anfasst.
 */

/** Die vier Modi aus der Referenz. */
export type Auswahlmodus = 'replace' | 'add' | 'remove' | 'toggle';

/** Nur die Tasten, die zählen — kein DOM-Event nötig, damit die Ableitung testbar bleibt. */
export interface Modifikatoren {
  shiftKey?: boolean;
  ctrlKey?: boolean;
  metaKey?: boolean;
  altKey?: boolean;
}

/**
 * Die Ableitung aus der Eingabe. Reihenfolge ist bewusst:
 * **Alt vor Strg vor Shift** — „entfernen" ist die eindeutigste Absicht, „umschalten" die zweite.
 * Wer `Shift+Alt` drückt, meint eher entfernen als hinzufügen; ohne feste Reihenfolge entschiede
 * die Reihenfolge der `if`-Zweige zufällig.
 *
 * `metaKey` gilt gleichwertig zu `ctrlKey` — auf dem Mac ist Cmd die übliche Taste, und der
 * Hausplaner läuft auf beiden Plattformen.
 */
export function aufloeseAuswahlmodus(e: Modifikatoren): Auswahlmodus {
  if (e.altKey) return 'remove';
  if (e.ctrlKey || e.metaKey) return 'toggle';
  if (e.shiftKey) return 'add';
  return 'replace';
}

/** Das Ergebnis einer Auswahl-Anwendung: die ids **und** das Primärobjekt. */
export interface Auswahlstand {
  ids: string[];
  /** Das Objekt, dessen Eigenschaften das Panel zeigt. `null`, wenn nichts ausgewählt ist. */
  primaerId: string | null;
}

/**
 * Wendet einen Treffer auf den bestehenden Auswahlstand an — **rein**: nimmt einen Stand, gibt
 * einen neuen zurück, verändert nichts.
 *
 * Kante 3 des Auftrags: **wird das primäre Objekt abgewählt, rückt das zuletzt verbliebene nach**;
 * ist die Auswahl leer, wird `primaerId` zu `null`. Warum das letzte und nicht das erste: die
 * Auswahlreihenfolge bildet ab, woran der Nutzer zuletzt gearbeitet hat.
 */
export function wendeAuswahlAn(vorher: Auswahlstand, trefferId: string, modus: Auswahlmodus): Auswahlstand {
  const drin = vorher.ids.includes(trefferId);
  switch (modus) {
    case 'replace':
      return { ids: [trefferId], primaerId: trefferId };
    case 'add':
      // Doppeltes Hinzufügen erzeugt keine Dublette — es macht den Treffer nur zum Primärobjekt.
      return { ids: drin ? [...vorher.ids] : [...vorher.ids, trefferId], primaerId: trefferId };
    case 'remove':
      return ohne(vorher, trefferId);
    case 'toggle':
      return drin ? ohne(vorher, trefferId) : { ids: [...vorher.ids, trefferId], primaerId: trefferId };
    default:
      return vorher;
  }
}

/** Einen Treffer aus dem Stand nehmen und das Primärobjekt nachziehen (Kante 3). */
function ohne(vorher: Auswahlstand, id: string): Auswahlstand {
  const ids = vorher.ids.filter((x) => x !== id);
  if (vorher.primaerId !== id) return { ids, primaerId: vorher.primaerId };
  return { ids, primaerId: ids.length > 0 ? ids[ids.length - 1] : null };
}

/**
 * Klick auf **leere Fläche**. Ohne Modifikator hebt er die Auswahl auf; **mit** Modifikator nicht —
 * sonst verliert man beim Danebentreffen die ganze Mehrfachauswahl (Kante 5).
 */
export function klickInsLeere(vorher: Auswahlstand, e: Modifikatoren): Auswahlstand {
  const mitModifikator = Boolean(e.shiftKey || e.ctrlKey || e.metaKey || e.altKey);
  return mitModifikator ? vorher : { ids: [], primaerId: null };
}

/** Der leere Stand — eine Stelle, damit „nichts ausgewählt" überall dasselbe heißt. */
export const LEERE_AUSWAHL: Auswahlstand = { ids: [], primaerId: null };
