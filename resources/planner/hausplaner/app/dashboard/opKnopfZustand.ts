/**
 * AUF-59 — die drei Zustände der Icon-Zeile, unterscheidbar gemacht.
 *
 * **Der gemessene Mangel (1440 px, 15 Knöpfe, 9 gesperrt):** Zustand **bedienbar** und Zustand
 * **gesperrt** unterschieden sich **ausschließlich in der Icon-Farbe** (`rgb(35,42,49)` gegen
 * `rgb(167,174,183)`) und im Cursor. Rahmen, Hintergrund und Deckkraft waren **identisch**. Und
 * jeder Knopf trug einen Rahmen — auch der, der gar keinen Schalter darstellt. Die Zeile las sich
 * als Reihe gleichwertiger Kästchen; Yamas Satz dazu: *„einige Icon, die sind eingerahmt, manche
 * sind aktiv manche nicht."*
 *
 * **Die Forderung, wörtlich umgesetzt:**
 * 1. **Gesperrt unterscheidet sich in mindestens ZWEI Merkmalen** von bedienbar — nicht nur in der
 *    Icon-Farbe. Hier: Icon-Farbe **und** Hintergrund **und** Deckkraft.
 * 2. **Der Rahmen trägt den Schalter-Zustand**, nicht jeden Knopf. Nur ein eingeschalteter Schalter
 *    (Raster, Fang) ist eingerahmt; alle anderen sind rahmenlos.
 *
 * **Keine Sperre ändert sich.** Dieses Modul entscheidet **nicht**, ob ein Knopf bedienbar ist — es
 * beschreibt nur, wie der bereits feststehende Zustand aussieht. Ob gesperrt wird, sagt weiterhin
 * die Stelle, die `disabled` setzt.
 *
 * **Keine Farbwerte:** Es liefert **Token-Namen**. Welche Farbe dahinter liegt, entscheidet
 * `studioDaten.ts` — so bleibt die Regel ohne Renderer prüfbar (Muster `werkzeugZustand.ts`,
 * `speicherAnzeige.ts`).
 */

/** Der Zustand, in dem ein Knopf der Icon-Zeile steckt. */
export type OpZustand = 'schalter-ein' | 'bedienbar' | 'gesperrt';

export interface OpKnopfBild {
  zustand: OpZustand;
  /** Token des Rahmens — `null` heißt **kein** Rahmen. */
  rahmenToken: 'brandInk' | null;
  /** Token des Hintergrunds. */
  grundToken: 'brandWash' | 'surface' | 'hair2';
  /** Token der Icon-Farbe. */
  iconToken: 'brandInk' | 'ink' | 'faint';
  deckkraft: number;
  cursor: 'pointer' | 'not-allowed';
}

/**
 * Der Zustand aus den beiden Tatsachen, die die Oberfläche kennt.
 * **`gesperrt` schlägt `aktiv`** — ein eingeschalteter Schalter, der gerade nicht bedienbar ist,
 * darf nicht wie ein bedienbarer aussehen.
 */
export function opZustand(aktiv: boolean, gesperrt: boolean): OpZustand {
  if (gesperrt) return 'gesperrt';
  return aktiv ? 'schalter-ein' : 'bedienbar';
}

/** Das Bild zum Zustand. Reine Abbildung, keine Entscheidung über Bedienbarkeit. */
export function opKnopfBild(aktiv: boolean, gesperrt: boolean): OpKnopfBild {
  const zustand = opZustand(aktiv, gesperrt);
  switch (zustand) {
    case 'schalter-ein':
      // Der EINZIGE Zustand mit Rahmen — er ist das Merkmal des Schalters.
      return { zustand, rahmenToken: 'brandInk', grundToken: 'brandWash', iconToken: 'brandInk', deckkraft: 1, cursor: 'pointer' };
    case 'gesperrt':
      // Drei Merkmale gegenüber `bedienbar`: Grund, Icon-Farbe, Deckkraft.
      return { zustand, rahmenToken: null, grundToken: 'hair2', iconToken: 'faint', deckkraft: 0.6, cursor: 'not-allowed' };
    case 'bedienbar':
    default:
      return { zustand, rahmenToken: null, grundToken: 'surface', iconToken: 'ink', deckkraft: 1, cursor: 'pointer' };
  }
}

/**
 * Worin sich zwei Zustände unterscheiden — die Liste, an der die Forderung „mindestens zwei
 * Merkmale" geprüft wird. Der Cursor zählt **nicht** mit: er ist erst beim Zeigen sichtbar und war
 * schon vorher der einzige Unterschied neben der Farbe.
 */
export function unterschiede(a: OpKnopfBild, b: OpKnopfBild): string[] {
  const merkmale: Array<keyof OpKnopfBild> = ['rahmenToken', 'grundToken', 'iconToken', 'deckkraft'];
  return merkmale.filter((m) => a[m] !== b[m]).map(String);
}
