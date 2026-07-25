/**
 * AUF-49 — **Fokus und Tastatur im Dialog**, einmal gebaut statt dreimal.
 *
 * **Der gemessene Mangel:** `FachFlaeche` trug `role="dialog"`, `aria-modal` und Escape — aber
 * **keinen Fokuswechsel beim Öffnen, keine Fokusfalle, keine Fokus-Rückgabe beim Schließen**.
 * `ConfigWizard` trug die Dialogsemantik **gar nicht**: kein `role`, kein `aria-modal`, kein
 * Escape. Wer den Wizard öffnete, ließ den Fokus auf dem Knopf dahinter stehen; der erste
 * Tab-Sprung landete **hinter** dem Dialog, in einer Oberfläche, die der Nutzer gar nicht sieht.
 *
 * **Was ein modaler Dialog leisten muss** (WCAG 2.1.2 „No Keyboard Trap" gilt in beide Richtungen —
 * gefangen sein ist falsch, ungefangen ebenso):
 * 1. Beim Öffnen wandert der Fokus **hinein**.
 * 2. `Tab` und `Shift+Tab` laufen **im Kreis** innerhalb des Dialogs.
 * 3. `Escape` schließt.
 * 4. Beim Schließen kehrt der Fokus **dorthin zurück**, wo er herkam.
 *
 * **Warum ein eigenes Modul:** Es gibt drei Dialoge (`FachFlaeche`, `EngineFlaeche` über dieselbe
 * Hülle, `ConfigWizard`). Drei Fokusfallen wären drei Gelegenheiten, es unterschiedlich falsch zu
 * machen — dasselbe Argument wie bei der `ReiterLeiste` (AUF-27).
 *
 * **Die Indexrechnung ist rein und damit prüfbar**; den DOM-Teil kann die Testumgebung nicht
 * sehen (kein DOM), und das steht hier, statt es zu verschweigen.
 */
import { useEffect, type RefObject } from 'react';

/**
 * Was als fokussierbar gilt. Bewusst **ohne** `[tabindex="-1"]`: solche Elemente sind programmatisch
 * erreichbar, aber nicht Teil der Tab-Reihenfolge — sie in die Falle aufzunehmen hieße, den Nutzer
 * an Stellen zu führen, die die Tastatur sonst nie besucht.
 */
export const FOKUSSIERBAR = [
  'a[href]', 'button:not([disabled])', 'input:not([disabled])', 'select:not([disabled])',
  'textarea:not([disabled])', '[role="button"]:not([aria-disabled="true"])', '[tabindex]:not([tabindex="-1"])',
].join(', ');

/**
 * Der nächste Index im Kreis. **Rein** — genau die Rechnung, an der eine Fokusfalle scheitert,
 * wenn sie am Rand nicht umschlägt.
 */
export function naechsterIndex(anzahl: number, aktuell: number, rueckwaerts: boolean): number {
  if (anzahl <= 0) return -1;
  const schritt = rueckwaerts ? -1 : 1;
  // `aktuell < 0` heißt „Fokus liegt nicht im Dialog" — dann von vorn (bzw. hinten) beginnen.
  const basis = aktuell < 0 ? (rueckwaerts ? 0 : anzahl - 1) : aktuell;
  return (basis + schritt + anzahl) % anzahl;
}

/**
 * Fokus in den Dialog holen, ihn dort halten, beim Schließen zurückgeben — und `Escape` bedienen.
 *
 * `onSchliessen` bleibt beim Aufrufer: **dieses Modul entscheidet nicht, was Schließen bedeutet.**
 */
export function useDialogFokus(
  huelle: RefObject<HTMLElement | null>,
  onSchliessen: () => void,
): void {
  useEffect(() => {
    const vorher = document.activeElement as HTMLElement | null;
    const knoten = huelle.current;
    if (knoten) {
      const erste = knoten.querySelectorAll<HTMLElement>(FOKUSSIERBAR)[0];
      // Kein fokussierbares Element? Dann bekommt der Dialog selbst den Fokus — sonst bliebe er
      // draußen, und die Falle unten hätte nichts zu fangen.
      if (erste) erste.focus();
      else { knoten.setAttribute('tabindex', '-1'); knoten.focus(); }
    }

    const beiTaste = (e: KeyboardEvent): void => {
      if (e.key === 'Escape') { onSchliessen(); return; }
      if (e.key !== 'Tab') return;
      const k = huelle.current;
      if (!k) return;
      const ziele = [...k.querySelectorAll<HTMLElement>(FOKUSSIERBAR)];
      if (ziele.length === 0) return;
      const aktuell = ziele.indexOf(document.activeElement as HTMLElement);
      e.preventDefault();
      ziele[naechsterIndex(ziele.length, aktuell, e.shiftKey)]?.focus();
    };

    document.addEventListener('keydown', beiTaste);
    return () => {
      document.removeEventListener('keydown', beiTaste);
      // Zurückgeben, wo er herkam — aber nur, wenn das Element noch im Dokument steht.
      if (vorher && document.contains(vorher)) vorher.focus();
    };
  }, [huelle, onSchliessen]);
}

/**
 * AUF-49 — die Tastatur-Behandlung einer **selbstgebauten** Schaltfläche (`role="button"`).
 *
 * **Gemessen:** acht solcher Flächen, davon reagierten **sieben nur auf Enter**. Ein echtes
 * `<button>` löst auf **Enter und Leertaste** aus (WCAG 2.1.1); wer die Rolle übernimmt, übernimmt
 * auch die Tastatur. Die Leertaste braucht zusätzlich `preventDefault`, sonst scrollt die Seite,
 * während sie auslöst.
 */
export function istAusloeser(e: { key: string; preventDefault: () => void }): boolean {
  if (e.key === 'Enter') return true;
  if (e.key === ' ' || e.key === 'Spacebar') { e.preventDefault(); return true; }
  return false;
}
