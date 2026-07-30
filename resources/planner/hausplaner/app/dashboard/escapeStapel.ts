/**
 * AUF-83-T5 / K-03 — **Escape bekommt eine Rangfolge, als Daten statt als Zufall des Anhängens.**
 *
 * **Der gemessene Mangel:** sechs Vorkommen von `key === 'Escape'` in vier Dateien
 * (`HausplanerApp.tsx`, `GeschossFlaeche.tsx`, `WerkzeugGruppenMenue.tsx`, `dialogFokus.ts`),
 * jedes mit einem eigenen `document.addEventListener('keydown', …)`. Sind zwei Ebenen gleichzeitig
 * offen — etwa die Befehlspalette UND das Geschoss-Menü —, feuern **beide** Listener auf denselben
 * Tastendruck und schließen **beide** Ebenen. Das ist kein Vorrang, das ist die Reihenfolge, in der
 * die Listener angehängt wurden — ein Zufall, der sich beim nächsten Umbau anders entscheidet.
 *
 * **Die Lösung, wörtlich Yamas Rangfolge:** `Palette > Dialog > Menue > Schiene > Werkzeug-Reset`,
 * als Daten in `RANGFOLGE` — dasselbe Muster wie `schienenReiter.ts`/`panelTabs.ts` für
 * Reiter-Reihenfolgen. **Jede Ebene behält ihren eigenen `document`-Listener** (kein einziger
 * globaler Listener, der bei jeder neuen Ebene umgebaut werden müsste) — sie fragt nur, BEVOR sie
 * handelt, ob sie gerade die oberste aktive ist. Ist sie es nicht, tut sie nichts; eine andere
 * Ebene ist dran.
 *
 * **Bei Gleichstand gewinnt die zuletzt geöffnete** (zuletzt registriert, zuerst geschlossen) —
 * dieselbe Konvention wie ein literaler Stapel. Kommt kein Fall dieser Art vor (zwei Menüs
 * gleichzeitig offen ist heute kein Bedienpfad), ist die Regel trotzdem definiert, statt undefiniert
 * zu bleiben.
 *
 * **Was dieses Modul NICHT tut:** Tab-Fallen, Fokus-Rückgabe, Klick-außerhalb — die bleiben, wo sie
 * sind (`dialogFokus.ts` für Tab, jede Komponente für Klick-außerhalb). Eine Ebene hier zu
 * registrieren heißt nur: „ich bin offen, und DAS hier schließt mich."
 */
import { useEffect } from 'react';

/** Die Ebenen, die um Escape konkurrieren — wörtlich Yamas Rangfolge, höchste zuerst. */
export type EscapeEbenenArt = 'palette' | 'dialog' | 'menue' | 'schiene' | 'werkzeug-reset';

/** Rangfolge als Daten. Index = Rang; kleinerer Index gewinnt. */
export const RANGFOLGE: readonly EscapeEbenenArt[] = ['palette', 'dialog', 'menue', 'schiene', 'werkzeug-reset'];

function rang(art: EscapeEbenenArt): number {
  const i = RANGFOLGE.indexOf(art);
  return i === -1 ? RANGFOLGE.length : i;
}

interface Eintrag {
  readonly id: number;
  readonly art: EscapeEbenenArt;
}

/** Die aktuell offenen Ebenen, in Registrierreihenfolge. Modul-Zustand — bewusst, siehe oben. */
const AKTIVE: Eintrag[] = [];
let naechsteId = 0;

/**
 * Die oberste aktive Ebene: niedrigster Rang, bei Gleichstand die zuletzt registrierte.
 *
 * **Rein und damit ohne DOM prüfbar** — dieselbe Trennung wie bei `dialogFokus.naechsterIndex`.
 */
export function oberste(liste: readonly Eintrag[]): Eintrag | undefined {
  return liste.reduce<Eintrag | undefined>((bisher, e) => {
    if (!bisher) return e;
    // `<=` statt `<`: bei Gleichstand überschreibt die spätere (zuletzt geöffnete) die frühere.
    return rang(e.art) <= rang(bisher.art) ? e : bisher;
  }, undefined);
}

/** Nur für Tests: der Registrierstand, ohne die Liste selbst preiszugeben. */
export function anzahlAktiv(): number {
  return AKTIVE.length;
}

/**
 * Eine Ebene an der Rangfolge anmelden, solange `aktiv` gilt. Drückt jemand Escape, während diese
 * Ebene die oberste aktive ist, läuft `onSchliessen` — sonst nicht.
 *
 * **`aktiv=true` fest** ist erlaubt und gemeint für Ebenen, die per React-Bedingung nur gerendert
 * werden, während sie offen sind (`{offen && <Dialog/>}`) — der Hook-Aufruf selbst läuft dann nur
 * während des offenen Zustands, `aktiv` ist der Sonderfall für Ebenen, die immer gemountet bleiben
 * und ihre Offenheit über ein Flag tragen (z. B. die linke Schiene).
 */
export function useEscapeEbene(art: EscapeEbenenArt, aktiv: boolean, onSchliessen: () => void): void {
  useEffect(() => {
    if (!aktiv || typeof document === 'undefined') return undefined;
    const eintrag: Eintrag = { id: naechsteId++, art };
    AKTIVE.push(eintrag);

    const beiTaste = (e: KeyboardEvent): void => {
      if (e.key !== 'Escape') return;
      const oben = oberste(AKTIVE);
      if (oben?.id !== eintrag.id) return; // eine andere Ebene ist dran
      onSchliessen();
    };
    document.addEventListener('keydown', beiTaste);

    return () => {
      document.removeEventListener('keydown', beiTaste);
      const i = AKTIVE.indexOf(eintrag);
      if (i >= 0) AKTIVE.splice(i, 1);
    };
  }, [art, aktiv, onSchliessen]);
}
