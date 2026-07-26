/**
 * AUF-72 — **die Bühnenhöhe: gemessen statt geschätzt.**
 *
 * **Der Befund, zweimal unabhängig gemessen:**
 * ```
 * Generator 1440×900   Bühne 804 px, unten y=1127  ⇒ 227 px unter dem Fenster, 72 % sichtbar
 * Planner   1440×813   Bühne 717 px, unten y=1086  ⇒ 273 px unter dem Fenster, 62 % sichtbar
 * ```
 * **Die Ursache ist eine Zahl, die einmal gestimmt hat:** `window.innerHeight − 96`. Die 96 stammt
 * aus einer Zeit mit **einer** Leiste über der Bühne. Seither sind der Arbeitsbereich-Wähler
 * (AUF-34), die Werkzeugzeile und die Optionszeile dazugekommen — gemessen stehen heute **323–369 px**
 * über der Bühne. Jede der drei Änderungen hat die Konstante still verstellt.
 *
 * **Deshalb ein Maßband statt einer Schätzung.** Die Höhe wird an dem Element genommen, das die
 * Bühne trägt. Kommt oben eine Zeile dazu oder fällt eine weg, stimmt sie automatisch mit — es gibt
 * keine Zahl mehr, die jemand nachpflegen müsste und vergessen könnte.
 *
 * **Warum ein Beobachter am Element und nicht nur ein Fenster-Zuhörer:** Erscheint eine Zeile über
 * der Bühne, ändert sich das **Fenster nicht**. Ein `resize`-Zuhörer bemerkt genau diesen Fall
 * nicht — also den, der den Fehler überhaupt erzeugt hat.
 *
 * **Keine Rückkopplung:** Gemessen wird die Inhaltsreihe (`flex: 1, overflow: hidden`). Ihre Höhe
 * ergibt sich aus dem Fenster minus der Zeilen darüber — **nicht** aus der Bühne, die in ihr liegt.
 * Die Bühne kann also nicht ihre eigene Messung verschieben; ein Messen⇒Zustand⇒Layout⇒Messen-Kreis
 * entsteht nicht.
 */
import { useEffect, useState, type RefObject } from 'react';

/**
 * Die Höhe ohne DOM — für den Testlauf, der kein Fenster hat. **Unverändert 700**, damit sich an
 * bestehenden Tests nichts verschiebt.
 */
export const ERSATZ_HOEHE = 700;

/**
 * Die kleinste Bühne, die noch eine Zeichenfläche ist. Unterhalb davon ist nichts mehr zu zeichnen;
 * der Wert ist benannt, damit niemand raten muss, ab wann „zu klein" beginnt.
 */
export const MIN_HOEHE = 200;

/**
 * Die Bühnenhöhe aus einer Messung.
 *
 * **Die Kante, an der so etwas bricht:** Beim ersten Rendern ist die gemessene Höhe **0** — das
 * Element steht noch nicht. Eine Bühne mit Höhe 0 ist ein **leerer Bildschirm**; deshalb gilt in
 * diesem Fall die Ersatzhöhe, nicht die 0.
 *
 * Eine **echte** Messung gilt dagegen, auch wenn sie klein ausfällt — sie ist die Wahrheit über den
 * vorhandenen Platz. Nur unter `MIN_HOEHE` wird aufgerundet, damit aus einem winzigen Fenster kein
 * unbenutzbarer Streifen wird.
 */
export function buehnenHoehe(gemessen: number | null): number {
  if (gemessen === null || gemessen <= 0) {
    return ERSATZ_HOEHE;
  }
  return Math.max(MIN_HOEHE, gemessen);
}

/**
 * Misst die Höhe des tragenden Elements und hält sie aktuell.
 *
 * Gibt `null` zurück, solange nichts gemessen ist — die Unterscheidung „noch nicht gemessen" von
 * „gemessen 0" bleibt damit erhalten und wird in `buehnenHoehe` entschieden, nicht hier.
 */
export function useGemesseneHoehe(traeger: RefObject<HTMLElement | null>): number | null {
  const [hoehe, setHoehe] = useState<number | null>(null);

  useEffect(() => {
    const knoten = traeger.current;
    if (!knoten || typeof ResizeObserver === 'undefined') {
      return undefined;
    }

    // Nur setzen, wenn sich der Wert wirklich ändert: ein unveränderter Zustand löst kein Rendern
    // aus und kann damit auch keine Schleife tragen.
    const messen = (): void => {
      const h = Math.round(knoten.getBoundingClientRect().height);
      setHoehe((alt) => (alt === h ? alt : h));
    };

    messen();
    const beobachter = new ResizeObserver(messen);
    beobachter.observe(knoten);
    // Das Fenster zusätzlich: der Beobachter deckt den Fall schon ab, aber ein Zoom- oder
    // Zoomstufen-Wechsel des Browsers meldet sich zuverlässiger über `resize`.
    window.addEventListener('resize', messen);
    return () => {
      beobachter.disconnect();
      window.removeEventListener('resize', messen);
    };
  }, [traeger]);

  return hoehe;
}
