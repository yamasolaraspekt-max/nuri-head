/**
 * AUF-47 — was der Speicher-Knopf und die Plakette **sagen dürfen**.
 *
 * **Der gemessene Widerspruch:** Die Studio-Testfläche setzt bewusst **kein** `data-speichern-url`
 * (`studio.blade.php:56`) — sie kann nicht speichern, und das ist so gewollt. `save()` steigt
 * daraufhin **still** aus. Der Knopf war trotzdem grün, primär und unbedingt aktiv, und die
 * Plakette daneben sagte **„Gespeichert"** — auf einer Fläche, die noch nie etwas gespeichert hat,
 * direkt neben dem Warnhinweis „Testfläche — wird NICHT gespeichert" in derselben Kopfzeile.
 *
 * **Zwei Aussagen, die vorher eine waren:** *„nichts zu speichern"* und *„hier kann gar nicht
 * gespeichert werden"*. `speicherStatus` beantwortet die erste; die zweite steht in
 * `speichernUrl` — sie wurde nur nie gelesen.
 *
 * **Der `save()`-No-Op bleibt.** Er ist gewollt; falsch war nur, ihn wie einen Erfolg aussehen zu
 * lassen. Dieses Modul ändert **nichts** am Speichern, sondern ausschließlich an dem, was die
 * Oberfläche darüber behauptet.
 *
 * **Ohne Farbwerte:** Es liefert eine `art`, keine Farbe. Welche Token dazugehören, entscheidet die
 * Oberfläche — so bleibt die Regel ohne Renderer prüfbar (Muster `werkzeugZustand.ts`).
 */
import type { SpeicherStatus } from '../../store/hausplanerStore';

/** Wie schwer wiegt die Aussage? Die Oberfläche bildet das auf ihre Token ab. */
export type AnzeigeArt = 'ok' | 'warnung' | 'neutral' | 'fehler';

export interface SpeicherAnzeige {
  /** Der Text der Plakette. */
  text: string;
  art: AnzeigeArt;
  /** Ist der Speichern-Knopf gesperrt? */
  gesperrt: boolean;
  /** Was der Knopf im Tooltip sagt — bei gesperrt **warum**, nie nur „Speichern". */
  knopfTitel: string;
}

/**
 * Die Anzeige zu Zustand **und** Fähigkeit.
 *
 * **`kannSpeichern === false` schlägt jeden Zustand.** Ob „ungespeicherte Änderungen" vorliegen,
 * ist auf einer Fläche ohne Ziel belanglos — und „Gespeichert" wäre dort schlicht unwahr.
 */
export function speicherAnzeige(status: SpeicherStatus, kannSpeichern: boolean, konfliktRevision?: number | null): SpeicherAnzeige {
  if (!kannSpeichern) {
    return {
      text: 'Testfläche — wird nicht gespeichert',
      art: 'warnung',
      gesperrt: true,
      knopfTitel: 'Diese Fläche hat kein Speicherziel. Der Plan am Objekt wird gespeichert, diese Testfläche nicht.',
    };
  }
  switch (status) {
    case 'ungespeichert':
      return { text: 'Ungespeicherte Änderungen', art: 'warnung', gesperrt: false, knopfTitel: 'Änderungen speichern (Strg+S)' };
    case 'speichert':
      return { text: 'Wird gespeichert …', art: 'neutral', gesperrt: true, knopfTitel: 'Wird gerade gespeichert …' };
    case 'konflikt':
      return {
        text: `Konflikt: Plan wurde von anderer Seite geändert (Revision ${konfliktRevision ?? '?'}) — Seite neu laden`,
        art: 'fehler', gesperrt: true,
        knopfTitel: 'Erst neu laden — sonst würde der fremde Stand überschrieben.',
      };
    case 'fehler':
      return { text: 'Speichern fehlgeschlagen — erneut versuchen', art: 'fehler', gesperrt: false, knopfTitel: 'Erneut versuchen' };
    case 'gespeichert':
    default:
      return { text: 'Gespeichert', art: 'ok', gesperrt: false, knopfTitel: 'Es gibt nichts zu speichern — alle Änderungen sind gesichert.' };
  }
}
