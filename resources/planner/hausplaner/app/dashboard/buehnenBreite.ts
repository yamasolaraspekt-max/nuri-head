/**
 * AUF-83-T1a — **die Bühnenbreite: gemessen statt gerechnet.**
 *
 * **Der Befund:** `HausplanerApp.tsx:369` rechnete `window.innerWidth − 220 − 268`. Die beiden
 * Zahlen sind die Breiten der linken Schiene und des rechten Panels — **abgeschrieben, nicht
 * gemessen.** Solange das so steht, weiß die Bühne nichts von ihrem Behälter:
 *
 * - Klappt ein Panel zu, **erreicht das die Rechnung nicht** — die Bühne bleibt schmal und daneben
 *   steht Leerraum. Genau deshalb kann heute kein Panel als Overlay laufen (T5).
 * - Sitzt die Insel in einem Behälter, der schmaler ist als das Fenster (T1b, Ticket-Shell mit
 *   zwei eigenen Seitenleisten), rechnet sie am Fenster vorbei und schiebt sich unter die Falz.
 *
 * **Dieselbe Lösung, die die HÖHE seit AUF-72/73 hat.** Dort stand einmal `innerHeight − 96`; die
 * 96 stammte aus einer Zeit mit einer Leiste, und jede neue Zeile hat sie still verstellt. Der Satz
 * aus jenem Modul gilt hier wörtlich:
 *
 * > *„Wer stattdessen einen festen Betrag abzöge, hätte die alte Konstante nur durch eine kleinere
 * > ersetzt — und säße in vier Wochen wieder hier."*
 *
 * **Deshalb steht in diesem Modul keine Pixelkonstante für eine Schiene.** Gemessen wird der
 * tragende Behälter, und abgezogen wird, was die Schienen **tatsächlich** einnehmen — sie melden
 * sich über `data-schiene` selbst. Kommt eine Schiene dazu, fällt eine weg oder klappt eine zu,
 * stimmt die Breite von selbst mit; es gibt keine Zahl mehr, die jemand nachpflegen müsste.
 *
 * **Warum ein Beobachter am Element und nicht nur ein Fenster-Zuhörer:** klappt ein Panel zu,
 * ändert sich das **Fenster nicht**. Ein `resize`-Zuhörer bemerkt genau den Fall nicht, für den
 * dieses Modul gebaut wird.
 *
 * **Keine Rückkopplung:** gemessen wird die Inhaltsreihe und die Breite der Schienen — **nicht** die
 * Bühne, die dazwischen liegt. Die Bühne bekommt ihre Breite zugewiesen; würde sie mitgemessen,
 * entstünde ein Messen⇒Zustand⇒Layout⇒Messen-Kreis.
 */
import { useEffect, useState, type RefObject } from 'react';

/**
 * Die Breite ohne DOM — für den Testlauf, der kein Fenster hat.
 *
 * **Unverändert 712**, weil die alte Rechnung ohne Fenster `1200 − 220 − 268` ergab. Damit
 * verschiebt sich an bestehenden Tests nichts; derselbe Grund, aus dem `ERSATZ_HOEHE` bei 700 blieb.
 */
export const ERSATZ_BREITE = 712;

/**
 * Die schmalste Bühne, die noch eine Zeichenfläche ist. Benannt, damit niemand raten muss, ab wann
 * „zu schmal" beginnt — dieselbe Rolle wie `MIN_HOEHE`.
 */
export const MIN_BREITE = 200;

/**
 * Das Merkmal, mit dem sich eine Schiene zu erkennen gibt.
 *
 * **Warum ein Merkmal und keine Liste von Breiten:** eine Liste müsste gepflegt werden und wäre
 * beim ersten neuen Panel falsch — dieselbe Falle wie die 220 und die 268. Ein Element, das Platz
 * neben der Bühne beansprucht, trägt das Merkmal; alles andere geht die Rechnung nichts an.
 */
export const SCHIENEN_MERKMAL = 'data-schiene';

/**
 * Die Bühnenbreite aus einer Messung.
 *
 * **Die Kante, an der so etwas bricht:** beim ersten Rendern ist die gemessene Breite **0** — das
 * Element steht noch nicht. Eine Bühne mit Breite 0 ist ein leerer Bildschirm; deshalb gilt dann
 * die Ersatzbreite, nicht die 0.
 *
 * Eine **echte** Messung gilt dagegen, auch wenn sie klein ausfällt — sie ist die Wahrheit über den
 * vorhandenen Platz. Nur unter `MIN_BREITE` wird angehoben.
 */
export function buehnenBreite(gemessen: number | null): number {
  if (gemessen === null || gemessen <= 0) {
    return ERSATZ_BREITE;
  }
  return Math.max(MIN_BREITE, gemessen);
}

/**
 * Was von der Reihe übrig bleibt, wenn die Schienen ihren Platz genommen haben.
 *
 * **Abgerundet, nicht gerundet:** ein aufgerundetes Pixel ist genau das Pixel, das rechts wieder
 * heraussteht. Zu schmal ist bei einer Zeichenfläche harmlos, zu breit nicht — dieselbe Begründung
 * wie bei `sichtbareHoehe`.
 *
 * @param reihe    Breite des tragenden Elements
 * @param schienen die tatsächlich eingenommenen Breiten der Schienen daneben
 */
export function freieBreite(reihe: number, schienen: readonly number[]): number {
  const belegt = schienen.reduce((summe, b) => summe + Math.max(0, b), 0);
  return Math.floor(Math.max(0, reihe - belegt));
}

/**
 * Misst die Inhaltsreihe und die Schienen darin und hält den Wert aktuell.
 *
 * Gibt `null` zurück, solange nichts gemessen ist — die Unterscheidung „noch nicht gemessen" von
 * „gemessen 0" bleibt erhalten und wird in `buehnenBreite` entschieden, nicht hier.
 */
export function useGemesseneBreite(traeger: RefObject<HTMLElement | null>): number | null {
  const [breite, setBreite] = useState<number | null>(null);

  useEffect(() => {
    const knoten = traeger.current;
    if (!knoten || typeof ResizeObserver === 'undefined') {
      return undefined;
    }

    // Nur setzen, wenn sich der Wert wirklich ändert: ein unveränderter Zustand löst kein Rendern
    // aus und kann damit auch keine Schleife tragen.
    const messen = (): void => {
      const reihe = knoten.getBoundingClientRect().width;
      const schienen = [...knoten.querySelectorAll(`[${SCHIENEN_MERKMAL}]`)]
        .map((el) => el.getBoundingClientRect().width);
      const b = freieBreite(reihe, schienen);
      setBreite((alt) => (alt === b ? alt : b));
    };

    messen();
    const beobachter = new ResizeObserver(messen);
    beobachter.observe(knoten);
    // Die Schienen einzeln mitbeobachten: klappt eine zu, aendert sich die REIHE nicht, sondern
    // nur die Schiene. Genau dieser Fall ist der Grund fuer dieses Modul.
    for (const el of knoten.querySelectorAll(`[${SCHIENEN_MERKMAL}]`)) {
      beobachter.observe(el);
    }
    window.addEventListener('resize', messen);
    return () => {
      beobachter.disconnect();
      window.removeEventListener('resize', messen);
    };
  }, [traeger]);

  return breite;
}
