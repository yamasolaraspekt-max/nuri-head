/**
 * AUF-71 — **eine Beschreibung für „gesperrt", app-weit.**
 *
 * **Der Befund (Zustands-Inventur des Evaluators, `21b016a`):** vier Stellen beschrieben denselben
 * Zustand auf vier verschiedene Weisen. Der auffälligste Bruch war der leiseste — **Deckkraft 0,6
 * gegen 0,4**: zwei Flächen derselben Anwendung sagen dasselbe mit verschiedener Lautstärke, und
 * niemand hat das je entschieden. *(Beim Bauen kam eine fünfte Stelle mit **0,45** dazu, die in der
 * Inventur fehlte — `WerkzeugGruppenMenue`.)*
 *
 * **Kein offener Defekt:** auf allen Flächen unterschied sich gesperrt bereits messbar von frei.
 * Es geht um Einheitlichkeit und um die WCAG-Härtung, nicht um eine Reparatur.
 *
 * ## Eine Quelle, mehrere Leser
 *
 * Die Beschreibung ist und bleibt **`opKnopfBild`** (AUF-59). Diese Datei erfindet nichts: sie liest
 * den gesperrten Zustand **einmal** aus und übersetzt seine Token in Werte, damit auch Flächen
 * außerhalb der Icon-Zeile daraus lesen können. **`opKnopfBild` bleibt token-rein** — die
 * Übersetzung gehört hierher, nicht dorthin.
 *
 * ## Warum es zwei Textfarben gibt — gemessen, nicht erfunden
 *
 * Die Quelle nennt für das Bildzeichen `faint`. Auf Text angewandt wäre das ein Rückschritt:
 *
 * ```
 * faint auf hair2      2,03 : 1
 * muted auf hair2      4,54 : 1
 * faint auf surface    2,24 : 1
 * muted auf surface    5,01 : 1
 * ```
 *
 * **Ein Bildzeichen darf verblassen; eine Beschriftung muss lesbar bleiben.** Deshalb gibt es
 * `GESPERRT_BESCHRIFTUNG` — **abgeleitet aus derselben Quelle, mit einem Namen, der sagt, wofür sie
 * gilt**, und nicht als zweite Quelle danebengestellt. Das ist genau der Fall, den der Auftrag in
 * §3 Punkt 3 vorgesehen hat.
 *
 * ## Was hier NICHT steht
 *
 * Kein `aktiv`, kein `frei`. Es geht um **einen** Zustand. Wer die anderen mitvereinheitlicht, baut
 * drei Posten in einen.
 */
import { opKnopfBild } from './opKnopfZustand';
import { T } from '../studioDaten';

/** Der gesperrte Zustand, einmal aus der Quelle gelesen. */
const GESPERRT = opKnopfBild(false, true);

/** Token → Wert. Dieselbe Zuordnung wie in der Icon-Zeile, nur an einer Stelle statt in jeder Datei. */
const WERT: Record<string, string> = {
  brandInk: T.brandInk, brandWash: T.brandWash, surface: T.surface, hair2: T.hair2,
  ink: T.ink, faint: T.faint,
};

/**
 * Die Deckkraft eines gesperrten Elements. **Die eine Zahl** — vorher standen 0,4 · 0,45 · 0,6
 * nebeneinander.
 */
export const GESPERRT_DECKKRAFT = GESPERRT.deckkraft;

/**
 * Der Mauszeiger. **Er allein genügt nie:** für Tastatur- und Touch-Bedienung existiert kein
 * Zeiger. Jede gesperrte Fläche trägt zusätzlich ein nicht-farbliches Merkmal — Deckkraft, Rahmen
 * oder ein Zustandsattribut, das Vorleseprogramme lesen (`disabled`/`aria-disabled`).
 */
export const GESPERRT_ZEIGER = GESPERRT.cursor;

/** Der Hintergrund einer gesperrten Fläche. */
export const GESPERRT_GRUND = WERT[GESPERRT.grundToken];

/** Die Farbe eines gesperrten **Bildzeichens**. Darf verblassen — es trägt keinen Text. */
export const GESPERRT_ICON = WERT[GESPERRT.iconToken];

/**
 * Die Farbe einer gesperrten **Beschriftung**. Bewusst dunkler als `GESPERRT_ICON`: mit `faint`
 * läge der Kontrast bei **2,03 : 1**, mit diesem Wert bei **4,54 : 1** (auf `hair2`). Ein
 * gesperrter Knopf soll erkennbar gesperrt sein — **nicht unleserlich**.
 */
export const GESPERRT_BESCHRIFTUNG = T.muted;
