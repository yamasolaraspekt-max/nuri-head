/**
 * AUF-48 Scheibe 3 — **aus einem Tastendruck wird eine Absicht, sonst nichts.**
 *
 * **Die Trennung, die diese Scheibe zieht:** *welche* Absicht eine Taste trägt, ist eine reine
 * Abbildung — sie hängt nur vom Ereignis ab. *Wer* die Absicht ausführt (löschen, speichern,
 * Palette öffnen), bleibt in der Komponente, weil dort Store und Zustand liegen.
 *
 * **Warum das etwas wert ist:** die Zuordnung war bisher eine if/else-Kette **innerhalb** eines
 * `useEffect` — nicht aufrufbar, nicht prüfbar, und die Reihenfolge ihrer Zweige war nirgends
 * festgehalten. *Genau diese Reihenfolge trägt aber zwei bewusste Entscheidungen* (siehe
 * `PALETTE_SCHLUCKT_ALLES` und den `⌘K`-Vorrang unten). Als Funktion ist sie beides: aufrufbar und
 * belegbar.
 *
 * **Was hier NICHT passiert: nichts wird entschieden.** Die Belegung ist Zeichen für Zeichen die
 * bisherige — keine Taste kommt hinzu, keine fällt weg, keine wechselt ihre Bedeutung.
 */
import { toolFuerShortcut } from './tools/toolRegistry';

/** Was eine Taste bedeutet. `nichts` heißt: die Taste ist belegt mit nichts. */
export type AbsichtArt =
  | 'ignorieren'
  | 'loeschen'
  | 'rueckgaengig'
  | 'wiederholen'
  | 'speichern'
  | 'palette-oeffnen'
  | 'werkzeug'
  /** Z-05: Enter schliesst die laufende Kontur. WER das tut, entscheidet die Hauptfunktion. */
  | 'kontur-schliessen'
  | 'nichts';

export interface Absicht {
  art: AbsichtArt;
  /** Nur bei `werkzeug`: die id aus der Registry. */
  werkzeugId?: string;
  /**
   * Soll das Ereignis unterdrückt werden?
   *
   * **Nur bei den vier Betriebssystem-Kürzeln** (`⌘Z`, `⌘Y`, `⌘S`, `⌘K`) — dort würde der Browser
   * sonst seine eigene Bedeutung ausführen (Rückgängig im Eingabefeld, Seite speichern, Suche).
   * **Löschen und die Werkzeug-Kürzel unterdrücken NICHT** — das war schon so und bleibt so.
   */
  preventDefault: boolean;
}

/** Das Ereignis, auf die Felder reduziert, die die Zuordnung wirklich liest. */
export interface TastenEreignis {
  key: string;
  ctrlKey: boolean;
  metaKey: boolean;
  /** Steht der Zeiger in einem Eingabefeld? (`e.target` ist ein `INPUT`) */
  zielIstEingabe: boolean;
  /** Ist die Befehlspalette offen? */
  paletteOffen: boolean;
}

const IGNORIEREN: Absicht = { art: 'ignorieren', preventDefault: false };
const NICHTS: Absicht = { art: 'nichts', preventDefault: false };

/**
 * **Kante 8, wörtlich aus dem Bestand:** solange die Palette offen ist, dürfen die Werkzeug-Kürzel
 * nicht durchschlagen — sonst wechselt ein Tastendruck im Filterfeld das Werkzeug. **Escape
 * schließt die Palette über den Escape-Stapel** (`escapeStapel.ts`), nicht hier; diese Zuordnung
 * verhindert nur, dass irgendetwas anderes durchkommt.
 */
export const PALETTE_SCHLUCKT_ALLES = true;

/**
 * Die Zuordnung. **Die Reihenfolge der Zweige ist die Aussage** und deshalb hier festgehalten:
 *
 * 1. Eingabefeld → nichts tun (man tippt)
 * 2. Palette offen → nichts tun (Kante 8)
 * 3. `Delete` / `Backspace` → löschen
 * 4. `⌘Z` · `⌘Y` · `⌘S` → Verlauf und Speichern
 * 5. `⌘K` → Palette. **Dieser Zweig steht VOR dem Kürzel-Zweig**, sonst griffe „K" (Registry-Kürzel
 *    von „Decke") auch mit Strg/⌘ — der Kürzel-Zweig prüft die Modifikatoren nämlich **nicht**.
 *    *Ohne Modifikator bleibt „K" = Decke, und genau das ist gewollt.*
 * 6. sonst → Werkzeug-Kürzel aus der Registry
 */
export function tastenAbsicht(e: TastenEreignis): Absicht {
  if (e.zielIstEingabe) return IGNORIEREN;
  if (e.paletteOffen) return IGNORIEREN;

  if (e.key === 'Delete' || e.key === 'Backspace') {
    return { art: 'loeschen', preventDefault: false };
  }

  // Z-05: Enter hatte bisher KEINE Bedeutung (fiel auf `nichts`). Die beiden Waechter oben halten
  // weiterhin: in einem Eingabefeld und bei offener Palette kommt es hier gar nicht an.
  if (e.key === 'Enter') {
    return { art: 'kontur-schliessen', preventDefault: false };
  }

  const mod = e.ctrlKey || e.metaKey;
  const kleines = e.key.toLowerCase();
  if (mod && kleines === 'z') return { art: 'rueckgaengig', preventDefault: true };
  if (mod && kleines === 'y') return { art: 'wiederholen', preventDefault: true };
  if (mod && kleines === 's') return { art: 'speichern', preventDefault: true };
  if (mod && kleines === 'k') return { art: 'palette-oeffnen', preventDefault: true };

  // **Bewusst ohne Modifikator-Prüfung** — so war es, und der `⌘K`-Zweig oben ist genau deshalb
  // vorgezogen. Wer das hier ändert, ändert Verhalten und braucht einen eigenen Auftrag.
  const werkzeug = toolFuerShortcut(e.key);
  if (werkzeug && werkzeug.art === 'werkzeug') {
    return { art: 'werkzeug', werkzeugId: werkzeug.id, preventDefault: false };
  }

  return NICHTS;
}
