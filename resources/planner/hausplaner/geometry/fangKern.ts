/**
 * Hausplaner L3b — Fang-Kern (Snapping), reine Funktion ohne Konva/three.
 *
 * Bestimmt für einen Rohpunkt den gefangenen Punkt + die Fang-Art. Priorität:
 * Endpunkt (nächster Bezugspunkt in Toleranz) > Ortho (0/90° zu einem Referenzpunkt) > Raster.
 * „Eine Wahrheit": es gibt genau EINE Fang-Entscheidung; der Aufrufer rechnet Bildschirm-px über
 * den Zoom in mm-Toleranz um und rendert den Indikator nach `art`. Nie stilles Fangen: `art`
 * benennt, WAS gefangen wurde (oder 'keiner').
 */

/**
 * Z-04: **drei Arten kommen dazu, und die Rangfolge ist Teil des Vertrags.**
 *
 * ```text
 * endpunkt > mittelpunkt > achse > verlaengerung > ortho > raster > keiner
 * ```
 *
 * *Ohne feste Rangfolge springt der Fang bei dichten Grundrissen zwischen zwei Kandidaten hin und
 * her — und das sieht aus wie ein Wackelkontakt, nicht wie eine Entscheidung.*
 *
 * **`ortho` behaelt seinen Platz ueber `raster`.** Die drei neuen Arten schieben sich darueber,
 * nicht dazwischen: sie brauchen je einen EIGENEN Operanden, und ohne ihn koennen sie gar nicht
 * feuern. *So bleibt jedes Verhalten, das es vor Z-04 gab, unveraendert — nachgewiesen an den
 * zwoelf Zusagen des Kerns, die keine der neuen Optionen uebergeben.*
 */
export type FangArt =
  | 'endpunkt'
  | 'mittelpunkt'
  | 'achse'
  | 'verlaengerung'
  | 'ortho'
  | 'raster'
  | 'keiner';

export interface FangPunkt {
  x: number;
  y: number;
}

export interface FangOptionen {
  /** Endpunkt-Toleranz in mm (Aufrufer: px→mm via Zoom). */
  toleranzMm: number;
  /** Rasterweite in mm (0/undefined = kein Raster). */
  raster?: number;
  /** Referenzpunkt für Ortho (z. B. Wandanfang). Ohne diesen kein Ortho-Fang. */
  ortho?: FangPunkt;
  /** Achs-Toleranz für Ortho in mm (Default = toleranzMm·2). */
  orthoToleranzMm?: number;
  /**
   * Z-04: **Wandmitten.** Getrennt von `kandidaten`, weil die Rangfolge sie unterscheidet — im
   * selben Topf waere ein Mittelpunkt genauso stark wie ein Endpunkt, und bei einer kurzen Wand
   * laege beides innerhalb derselben Toleranz.
   */
  mitten?: ReadonlyArray<FangPunkt>;
  /**
   * Z-04: **Wandachsen** — je zwei Punkte, die eine Gerade aufspannen. Gefangen wird auf die
   * VERLAENGERUNG dieser Geraden, nicht auf die Strecke: das ist der Fall „an der Flucht der
   * Nachbarwand ausrichten".
   */
  achsen?: ReadonlyArray<readonly [FangPunkt, FangPunkt]>;
  /**
   * Z-04: **der laufende Weg** — `von` -> `ueber`. Gefangen wird auf die gerade Fortsetzung
   * darueber hinaus. *Nicht dasselbe wie `ortho`:* `ortho` richtet auf 0/90 Grad aus, dies hier
   * setzt die begonnene Richtung fort, welche auch immer sie ist.
   */
  weg?: readonly [FangPunkt, FangPunkt];
  /** Fang global an? (entspricht settings.snapEnabled). Default true. */
  aktiv?: boolean;
}

export interface FangErgebnis {
  punkt: FangPunkt;
  art: FangArt;
}

const runde = (p: FangPunkt): FangPunkt => ({ x: Math.round(p.x), y: Math.round(p.y) });

/**
 * **Der Lotfusspunkt von `p` auf die GERADE durch `a` und `b`** — nicht auf die Strecke.
 *
 * *Der Unterschied ist der ganze Zweck:* wer an der Flucht einer Nachbarwand ausrichtet, steht
 * meistens NEBEN ihr, nicht auf ihr. Ein Lot auf die Strecke gaebe dort den Endpunkt zurueck und
 * damit denselben Fang, den es schon gibt.
 *
 * `null` bei entarteter Achse (zwei gleiche Punkte): durch einen Punkt geht keine Gerade, und eine
 * Division durch null lieferte `NaN`.
 *
 * **Warum diese Funktion ausgefuehrt wird, obwohl sie ein Innenteil ist.** In der Mutationsprobe
 * blieb das Entfernen des Waechters BLIND — und das war kein Loch in der Zusage, sondern eine
 * Eigenschaft der Lage: ohne ihn kommt `{NaN, NaN}` zurueck, `NaN <= toleranz` ist `false`, der
 * Fang faellt durch, und von aussen sieht alles gleich aus. *Eine Zusage kann nicht halten, was
 * nach aussen nicht sichtbar ist.* Der Vertrag „entartete Achse ergibt kein Ergebnis" ist aber
 * echt und soll nicht vom Zufall abhaengen, dass NaN sich hier gutmuetig verhaelt — deshalb wird
 * er direkt geprueft, statt eine unpruefbare Behauptung im Kommentar stehen zu lassen.
 */
export function lotAufGerade(p: FangPunkt, a: FangPunkt, b: FangPunkt): FangPunkt | null {
  const dx = b.x - a.x;
  const dy = b.y - a.y;
  const laenge2 = dx * dx + dy * dy;
  if (laenge2 < 1e-9) {
    return null;
  }
  const t = ((p.x - a.x) * dx + (p.y - a.y) * dy) / laenge2;

  return { x: a.x + t * dx, y: a.y + t * dy };
}

export function fange(
  p: FangPunkt,
  kandidaten: ReadonlyArray<FangPunkt>,
  opt: FangOptionen,
): FangErgebnis {
  if (opt.aktiv === false) {
    return { punkt: runde(p), art: 'keiner' };
  }

  // 1) Endpunkt — nächster Kandidat innerhalb Toleranz gewinnt.
  let besterAbstand = Infinity;
  let bester: FangPunkt | null = null;
  for (const k of kandidaten) {
    const d = Math.hypot(p.x - k.x, p.y - k.y);
    if (d <= opt.toleranzMm && d < besterAbstand) {
      besterAbstand = d;
      bester = k;
    }
  }
  if (bester) {
    return { punkt: runde(bester), art: 'endpunkt' };
  }

  // 2) Mittelpunkt — dieselbe Toleranz, aber schwaecher als ein Endpunkt.
  if (opt.mitten && opt.mitten.length > 0) {
    let besteMitte: FangPunkt | null = null;
    let mAbstand = Infinity;
    for (const m of opt.mitten) {
      const d = Math.hypot(p.x - m.x, p.y - m.y);
      if (d <= opt.toleranzMm && d < mAbstand) {
        mAbstand = d;
        besteMitte = m;
      }
    }
    if (besteMitte) {
      return { punkt: runde(besteMitte), art: 'mittelpunkt' };
    }
  }

  // 3) Achse — Lot auf die VERLAENGERTE Gerade durch zwei Punkte.
  if (opt.achsen && opt.achsen.length > 0) {
    let besterFuss: FangPunkt | null = null;
    let aAbstand = Infinity;
    for (const [a, b] of opt.achsen) {
      const fuss = lotAufGerade(p, a, b);
      if (!fuss) {
        continue; // entartete Achse (zwei gleiche Punkte) — keine Gerade, kein Fang
      }
      const d = Math.hypot(p.x - fuss.x, p.y - fuss.y);
      if (d <= opt.toleranzMm && d < aAbstand) {
        aAbstand = d;
        besterFuss = fuss;
      }
    }
    if (besterFuss) {
      return { punkt: runde(besterFuss), art: 'achse' };
    }
  }

  // 4) Verlaengerung — die gerade Fortsetzung des laufenden Wegs.
  if (opt.weg) {
    const fuss = lotAufGerade(p, opt.weg[0], opt.weg[1]);
    if (fuss && Math.hypot(p.x - fuss.x, p.y - fuss.y) <= opt.toleranzMm) {
      return { punkt: runde(fuss), art: 'verlaengerung' };
    }
  }

  // 5) Ortho — nahe einer Achse durch den Referenzpunkt (0/90°).
  if (opt.ortho) {
    const otol = opt.orthoToleranzMm ?? opt.toleranzMm * 2;
    const dx = p.x - opt.ortho.x;
    const dy = p.y - opt.ortho.y;
    // näher an der Waagerechten (kleines |dy|) ⇒ y auf Referenz; sonst Senkrechte ⇒ x auf Referenz.
    if (Math.abs(dy) <= otol && Math.abs(dy) <= Math.abs(dx)) {
      return { punkt: runde({ x: p.x, y: opt.ortho.y }), art: 'ortho' };
    }
    if (Math.abs(dx) <= otol && Math.abs(dx) < Math.abs(dy)) {
      return { punkt: runde({ x: opt.ortho.x, y: p.y }), art: 'ortho' };
    }
  }

  // 6) Raster — auf die nächste Rasterlinie runden.
  if (opt.raster && opt.raster > 0) {
    const r = opt.raster;
    return { punkt: { x: Math.round(p.x / r) * r, y: Math.round(p.y / r) * r }, art: 'raster' };
  }

  return { punkt: runde(p), art: 'keiner' };
}


/**
 * **Der Fangradius in BILDSCHIRM-Pixeln** — und das ist der ganze Befund von Z-02.
 *
 * Bis heute stand im Bauteil ein fester Radius von 150 **Millimetern**. Millimeter sind
 * Weltkoordinaten; wie viel davon der Mensch am Schirm sieht, hängt am Zoom:
 *
 * ```text
 * Zoom (px pro mm)   150 mm entsprechen        12 px entsprechen
 *   0.02                   3 Bildschirmpixel        600 mm
 *   0.12 (Standard)       18 Bildschirmpixel        100 mm
 *   0.50                  75 Bildschirmpixel         24 mm
 * ```
 *
 * *Bei 0,02 war der Fang praktisch tot — drei Pixel trifft niemand. Bei 0,5 riss er den Zeiger
 * aus 75 Pixeln Entfernung an sich.* **Ein Radius, der sich nicht mit dem Zoom ändert, ist
 * entweder unbrauchbar oder aufdringlich, nie beides richtig.**
 */
export const FANG_PX = 12;

/**
 * **Bildschirm-Pixel in Welt-Millimeter, über den Zoom.**
 *
 * *Warum das hier steht und nicht im Bauteil:* die Umrechnung ist der Kern der Entscheidung von
 * Z-02, und im Rumpf einer React-Funktion wäre sie von keiner Zusage erreichbar. Hier ist sie
 * eine reine Funktion — prüfbar, ohne den Planer zu starten.
 *
 * **Der Wächter gegen `zoom = 0`:** eine Division durch null ergäbe `Infinity`, und eine
 * unendliche Toleranz fängt **jeden** Punkt auf den nächstbesten Endpunkt. Das sähe nicht nach
 * einem Fehler aus, sondern nach einem sehr eifrigen Fang. Deshalb fällt die Funktion auf den
 * Pixelwert zurück, statt still etwas Unsinniges zu liefern.
 */
export function toleranzAusZoom(zoom: number, fangPx: number = FANG_PX): number {
  if (!(zoom > 0)) {
    return fangPx;
  }

  return fangPx / zoom;
}

/** Eine Wandstrecke (für die Fangpunkt-Sammlung). */
export interface WandStrecke {
  start: FangPunkt;
  end: FangPunkt;
}

/**
 * Sammelt die Fang-Kandidaten aus den Wänden: Endpunkte + Mittelpunkt je Wand. Reine Ableitung —
 * der Aufrufer reicht das Ergebnis an fange(); so bleibt fange() geometriefrei und testbar.
 */
export function wandFangpunkte(waende: ReadonlyArray<WandStrecke>): FangPunkt[] {
  const pts: FangPunkt[] = [];
  for (const w of waende) {
    pts.push({ x: w.start.x, y: w.start.y });
    pts.push({ x: w.end.x, y: w.end.y });
    pts.push({ x: Math.round((w.start.x + w.end.x) / 2), y: Math.round((w.start.y + w.end.y) / 2) });
  }
  return pts;
}

/**
 * Z-03 — **was die Fussflaeche anzeigt.**
 *
 * *Der Fang war bisher stumm: er zog den Zeiger, und niemand erfuhr, WORAUF.* Bei dichten
 * Grundrissen sieht ein Endpunkt-Fang genauso aus wie ein Raster-Fang — bis man die Wand fertig
 * gezogen hat und die Laenge nicht stimmt.
 *
 * **`keiner` ergibt eine leere Zeichenkette, nicht das Wort „keiner".** Eine Statuszeile, die
 * dauernd „kein Fang" sagt, ist Rauschen; sie soll sich melden, wenn etwas passiert.
 */
export const FANG_TEXT: Record<FangArt, string> = {
  endpunkt: 'Endpunkt',
  mittelpunkt: 'Wandmitte',
  achse: 'Wandflucht',
  verlaengerung: 'Verlängerung',
  ortho: 'rechter Winkel',
  raster: 'Raster',
  keiner: '',
};
