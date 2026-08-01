/**
 * AUF-91 — **unter 1024 px sagt der Planer, dass er hier nicht bedienbar ist.**
 *
 * ---
 *
 * **Der Befund** (`PB-046`, Prüfer, aus der laufenden Anwendung):
 *
 * ```text
 * bei 375 px   acht Bedienelemente liegen bei x 588–710, also ausserhalb des Sichtfelds
 *              scrollWidth == 375 == Sichtfeldbreite  ->  KEIN Weg dorthin
 *              Modusschalter 390 px breit auf 375 px
 * bei 1024 und 1440 px   keine Beanstandung
 * ```
 *
 * **Das Ziel ist NICHT, den Planer dort bedienbar zu machen.** Ein CAD-Werkzeug auf einem Telefon
 * ist unverhältnismässig; die Vertagung auf L7 gilt unverändert. **Das Ziel ist, dass er es sagt.**
 * *Die ticket-Schale ist für kleine Bildschirme gebaut — wer über sie ankommt, landet heute auf
 * einer Oberfläche, die funktionstüchtig aussieht und acht Werkzeuge verschweigt. Das ist
 * schlechter als eine ehrliche Sperre.*
 *
 * ---
 *
 * **Woher die Schwelle kommt — und warum nicht aus der gemessenen Behälterbreite.**
 *
 * Das Blatt schlägt in der Begründung zu K-02 `buehnenBreite.ts` vor (*„der Schalter existiert, er
 * muss nur gelesen werden"*). **Gemessen trägt diese Quelle die Schwelle nicht:**
 *
 * ```text
 * Fenster   Behaelter (inhaltRef)
 *   1440           1077
 *   1100            737
 *   1024            661     <- schon hier weit unter 1024
 * ```
 *
 * *Eine Schwelle „Behälter < 1024" spränge bei 1100 px Fensterbreite an — genau dort, wo K-01
 * „unverändert" verlangt.* **Der Schalter, der die verlangte Schwelle wirklich trägt, steht
 * bereits in `HausplanerApp`:** `useIstSchmal()` mit `(max-width: 1023px)`, seit AUF-83-T5 in
 * Gebrauch für das Overlay-Verhalten der Schienen. **Er wird gelesen, nicht verdoppelt** — genau
 * das, was „kein zweiter Messweg" meint.
 *
 * **Und ausdrücklich kein `@media` in der Stilschicht:** die Zusage aus `stilschicht.test.ts`
 * (*„Responsive ist L7"*) bleibt grün. Die Abfrage lebt in JavaScript, wo sie schon lebte; die
 * Stilschicht bekommt nur Klassen ohne Bedingung.
 *
 * ---
 *
 * **Die Fläche hält keinen Zustand** (K-04). Sie bekommt gesagt, ob sie erscheint, und sonst
 * nichts. *Über 1024 px rendert sie `null` — kein zusätzliches Element im Baum, nicht einmal ein
 * verstecktes.*
 */
import React from 'react';

/** Die Breite, ab der der Planer bedienbar ist. **Eine Zahl, eine Stelle.** */
export const MINDESTBREITE_PX = 1024;

export interface MindestbreiteHinweisEigenschaften {
  /** Kommt aus `useIstSchmal()` — dem Schalter, den es schon gibt. */
  sichtbar: boolean;
}

/**
 * **Der Satz ist ehrlich, nicht endgültig** (K-03): er nennt, *dass* eine Mindestbreite nötig ist,
 * *welche*, und den *Weg zurück* — und er behauptet nicht, dass es nie gehen wird.
 * *L7 ist eine Vertagung, keine Absage.*
 */
export function MindestbreiteHinweis({ sichtbar }: MindestbreiteHinweisEigenschaften): React.ReactElement | null {
  if (!sichtbar) return null;
  return (
    <div className="hp-mb-flaeche" role="status">
      <div className="hp-mb-kasten">
        <p className="hp-mb-titel">Der Planer braucht mehr Breite</p>
        <p className="hp-mb-satz">
          Ab {MINDESTBREITE_PX} px Fensterbreite ist er vollständig bedienbar. Auf schmaleren
          Bildschirmen liegen Werkzeuge ausserhalb des sichtbaren Bereichs — sie sind dann nicht
          erreichbar, auch nicht durch Scrollen.
        </p>
        <p className="hp-mb-satz">
          Öffne das Fenster breiter oder wechsle an einen grösseren Bildschirm. Die übrigen
          Bereiche von SA-DESK funktionieren hier unverändert.
        </p>
        <p className="hp-mb-fussnote">
          Eine Bedienung auf schmalen Geräten ist geplant, aber noch nicht gebaut.
        </p>
      </div>
    </div>
  );
}
