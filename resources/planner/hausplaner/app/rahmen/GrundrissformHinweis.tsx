/**
 * Z1-W2-3 — **die Eckenanalyse erreicht den Benutzer.**
 *
 * ---
 *
 * **Der Befund:** `geometry/grundriss.ts` erkennt seit jeher, ob die Kontur eines Gebäudes zu der
 * Form passt, die für es behauptet wird — einspringende Innenwinkel zählen, mit der erwarteten
 * Zahl vergleichen, fertig. **Aufrufer im Produktivpfad: 0.** Die Analyse lief nur im Test.
 *
 * **Der Bedienweg ist der, den es ohnehin gibt:** ein Dach auswählen, im Eigenschaften-Panel die
 * Dachform umstellen. Wer dort `L-Dach` wählt, während das Polygon ein Rechteck ist, sieht es
 * jetzt. Kein Leisteneintrag — N4: *eine Warnung, die man erst anklicken muss, warnt nicht.*
 *
 * ---
 *
 * ## ⚠ Der Ausschnitt ist beschnitten, und das ist der Kern dieses Blattes
 *
 * `grundriss.ts` trägt einen **Herkunftsvermerk** (20.08.): es ist gegen `RoofEngine`/`buildFlat`
 * geschrieben, und die stehen **ausschliesslich** in einer Referenzdatei unter `docs/` — im
 * Produktivbaum 0 Definitionen. Dazu rechnet das Modul in **Metern** (`grundrissFlaecheM2`),
 * während die Domäne dieser Insel **ganze Millimeter** führt (`dickeMm`, `hoeheMm`, …).
 *
 * **Der Vermerk gilt — für die Funktionen, die Einheiten tragen.** Angeschlossen wird deshalb
 * ausdrücklich NUR:
 *
 * | angeschlossen | einheitenfrei, weil |
 * |---|---|
 * | `eckenAnalyse(poly)` | Punkte rein, Punkte raus |
 * | `anzahlInnenwinkel(poly)` | Polygon rein, **Zahl** raus |
 * | `erwarteteInnenwinkel(form)` | Form rein, **Zahl** raus |
 * | `formAusShape(shape)` | `'l-shape'` → `'l-form'`, reine Abbildung |
 *
 * **Nicht angeschlossen:** `grundrissPolygon` und `grundrissFlaecheM2` — dort gilt der Vermerk
 * weiter, und ein Anschluss wäre eine Umrechnung mit Rundungsentscheidung.
 *
 * *Eine Winkelanalyse ist **skalierungsinvariant**: ein einspringender Innenwinkel bleibt einer,
 * ob die Punkte Millimeter oder Meter tragen.* **Deshalb ist in diesem Ausschnitt nichts zu
 * runden — und nur deshalb ist er ohne Fachentscheidung baubar.**
 *
 * *Ich hatte das zunächst zu pauschal beurteilt und das ganze Blatt als blockiert gemeldet; der
 * Vermerk trifft das Modul, nicht jede seiner Funktionen. Berichtigt, bevor gebaut wurde.*
 */
import React from 'react';
import type { RoofNode } from '../../domain/scene.types';
import { anzahlInnenwinkel, erwarteteInnenwinkel, formAusShape } from '../../geometry/grundriss';

export interface GrundrissformHinweisEigenschaften {
  /** Das gewählte Dach. `null` = nichts zu prüfen. */
  dach: RoofNode | null;
}

/**
 * Zeigt an, ob die Kontur zur gewählten Dachform passt.
 *
 * **Ohne Abweichung erscheint eine Bestätigungszeile, kein Nichts** — sonst wäre „geprüft und
 * stimmig" von „gar nicht geprüft" nicht zu unterscheiden, und genau diese Verwechslung ist die
 * Rot-Lage, die dieses Blatt behebt.
 */
export function GrundrissformHinweis({ dach }: GrundrissformHinweisEigenschaften): React.ReactElement | null {
  if (!dach) return null;

  const polygon = dach.polygon ?? [];
  // Unter drei Punkten gibt es keine Ecken zu zählen — dann wird nicht geraten, sondern nichts
  // gezeigt. Ein Hinweis auf ein leeres Polygon nennt einen Mangel, den der Benutzer nicht
  // verursacht hat.
  if (polygon.length < 3) return null;

  const form = formAusShape(dach.roofType);
  const gezaehlt = anzahlInnenwinkel(polygon);
  const erwartet = erwarteteInnenwinkel(form);
  const passt = gezaehlt === erwartet;

  // **Kein neues Design.** Das Panel fuehrt fuer Befunde bereits `hp-ep-befund` samt
  // `hp-ep-schwere-symbol`/`-text`; die Regel dort lautet ausdruecklich *Schwere als Symbol UND
  // Text, nicht nur als Farbe* (A11y), und `align-items: flex-start` steht schon drin. Eine eigene
  // Klasse waere ein zweites Aussehen fuer dieselbe Sache — ich habe die vorhandenen gemessen und
  // benutze sie. *Bei Z1-W2-1 hatte ich eine Klasse benutzt, die es nicht gab; diesmal zuerst
  // nachgesehen.*
  if (!passt) {
    return (
      <div
        className="hp-ep-befund"
        data-pruefung="grundrissform"
        data-ergebnis="abweichung"
        data-form={form}
      >
        <span aria-hidden className="hp-ep-schwere-symbol">!</span>
        <span>
          <strong className="hp-ep-schwere-text">Form passt nicht</strong>
          {` – „${form}" erwartet ${erwartet} ${erwartet === 1 ? 'einspringende Ecke' : 'einspringende Ecken'}, `}
          {`die Kontur hat ${gezaehlt}. Dachform anpassen oder Kontur ändern.`}
        </span>
      </div>
    );
  }

  // **Ohne Abweichung erscheint eine Bestaetigungszeile, kein Nichts** — sonst waere „geprueft und
  // stimmig" von „gar nicht geprueft" nicht zu unterscheiden, und genau diese Verwechslung ist die
  // Rot-Lage, die dieses Blatt behebt.
  return (
    <div
      className="hp-ep-hinweis"
      data-pruefung="grundrissform"
      data-ergebnis="passt"
      data-form={form}
    >
      <span aria-hidden className="hp-ep-schwere-symbol">✓</span>
      {` Kontur passt zur Form „${form}" — ${gezaehlt} einspringende ${gezaehlt === 1 ? 'Ecke' : 'Ecken'}.`}
    </div>
  );
}
