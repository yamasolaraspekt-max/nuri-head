/**
 * Z-10 — **Länge tippen statt ziehen: die reine Rechnung.**
 *
 * ---
 *
 * **Wer ein Geschoss baut, hat Maße im Kopf, nicht Pixel.** `4200` zu tippen ist genauer als zu
 * ziehen und schneller. Diese Datei rechnet aus Startpunkt, Zeigerrichtung und Länge den
 * Zielpunkt — mehr nicht: kein React, kein Konva, keine Speicher-Wirkung.
 *
 * ---
 *
 * **Was das hier NICHT ist, und die Verwechslung droht wirklich.** `geometry/bemassung.ts` und
 * `geometry/masskette.ts` heißen fast so und existieren längst — sie **zeigen** Maße an. *Das ist
 * die Anzeigerichtung; dies hier ist die Eingaberichtung.* Wer beide verwechselt, baut ein
 * Kriterium, das vor dem Bau schon grün ist (F-07).
 *
 * ---
 *
 * **Die Richtung kommt aus dem Zeiger, nur die Länge aus dem Feld** — und wer will, tippt den
 * Winkel dazu. *Niemand muss.* Das hält den Bedienweg vertraut: man zielt weiter mit der Maus und
 * gibt nur das Maß genau an.
 */

/** Ein Punkt in Weltkoordinaten (mm). Bewusst lokal: dieses Modul kennt keine Szene. */
export interface MassPunkt {
  x: number;
  y: number;
}

/** Grad in Bogenmaß. */
const imBogen = (grad: number): number => (grad * Math.PI) / 180;

/**
 * **Ist diese Länge überhaupt eine Länge?**
 *
 * Ausgeführt und getrennt, damit die Ansicht dieselbe Frage stellen kann wie die Rechnung — und
 * nicht ihre eigene zweite Antwort baut. *Null ist keine Länge und negativ ist keine Richtung:
 * beides wäre kein Punkt, sondern ein stehengebliebener Zug.*
 */
export function istBrauchbareLaenge(laengeMm: unknown): boolean {
  return typeof laengeMm === 'number' && Number.isFinite(laengeMm) && laengeMm > 0;
}

/**
 * **Die Richtung vom Startpunkt zum Zeiger, auf Länge 1 normiert** — oder `null`, wenn es keine
 * gibt, weil beide Punkte aufeinanderliegen.
 *
 * **Ausgeführt und nicht im Rumpf versteckt, und das ist eine Lehre von gestern.** In der
 * Mutationsprobe blieb „keine Richtung wird zugelassen" BLIND, solange die Bedingung innen stand:
 * ohne sie kommt `0/0 = NaN` heraus, die Endlichkeitsprüfung weiter unten fängt es ab, und von
 * aussen sieht alles gleich aus. *Eine Zusage kann nicht halten, was nach aussen nicht sichtbar
 * ist* (B3) — der Vertrag „aufeinanderliegende Punkte ergeben keine Richtung" ist aber echt und
 * soll nicht davon abhängen, dass `NaN` sich gutmütig verhält.
 */
export function richtungAus(start: MassPunkt, zeiger: MassPunkt): { ex: number; ey: number } | null {
  const dx = zeiger.x - start.x;
  const dy = zeiger.y - start.y;
  const laenge = Math.hypot(dx, dy);
  if (!(laenge > 0)) {
    return null;
  }

  return { ex: dx / laenge, ey: dy / laenge };
}

/**
 * **Der Zielpunkt aus Startpunkt, Zeigerrichtung und Länge.**
 *
 * ```text
 * ohne Winkel   Richtung = die Gerade Start -> Zeiger, auf `laengeMm` normiert
 * mit Winkel    der Winkel SCHLAEGT den Zeiger (0° = nach rechts, gegen den Uhrzeigersinn)
 * ```
 *
 * **`null` heisst: kein Punkt, und zwar aus einem der drei Gruende** — keine brauchbare Länge,
 * keine Richtung (der Zeiger liegt auf dem Startpunkt), oder ein Ergebnis, das nicht endlich ist.
 *
 * *Der Fall „Zeiger genau auf dem Startpunkt" ist keine Spitzfindigkeit:* er tritt bei jedem
 * ersten Tastendruck auf, solange die Maus sich seit dem Klick nicht bewegt hat. Ohne diesen
 * Zweig teilte die Normierung durch null und lieferte `NaN` — und `NaN` sieht in einer
 * Koordinate aus wie ein Punkt, bis jemand die Wand misst.
 */
export function punktAusLaenge(
  start: MassPunkt,
  zeiger: MassPunkt,
  laengeMm: number,
  winkelGrad?: number,
): MassPunkt | null {
  if (!istBrauchbareLaenge(laengeMm)) {
    return null;
  }

  let ex: number;
  let ey: number;

  if (typeof winkelGrad === 'number' && Number.isFinite(winkelGrad)) {
    // **Der Winkel schlaegt den Zeiger.** Wer ihn tippt, meint ihn — sonst waere die Eingabe
    // eine Bitte und keine Angabe.
    const w = imBogen(winkelGrad);
    ex = Math.cos(w);
    ey = Math.sin(w);
  } else {
    const r = richtungAus(start, zeiger);
    if (!r) {
      return null; // keine Richtung — nicht durch null teilen
    }
    ex = r.ex;
    ey = r.ey;
  }

  const punkt = {
    x: Math.round(start.x + ex * laengeMm),
    y: Math.round(start.y + ey * laengeMm),
  };

  // **Sehr grosse Laengen duerfen kein `NaN` und kein `Infinity` ergeben.** Eine Koordinate, die
  // nicht endlich ist, wandert sonst in den Knoten und macht die ganze Szene unbrauchbar.
  if (!Number.isFinite(punkt.x) || !Number.isFinite(punkt.y)) {
    return null;
  }

  return punkt;
}

/**
 * **Der Zustand der laufenden Maßeingabe.** Rein, damit die Ansicht ihn nur noch anzeigt.
 *
 * `null` heisst: es läuft keine Eingabe. *Das ist NICHT dasselbe wie eine leere Eingabe —* eine
 * offene Eingabe mit leerem Feld wartet auf Ziffern, eine geschlossene tut gar nichts.
 */
export interface MassEingabe {
  laenge: string;
  winkel: string;
  /** Welches der beiden Felder Tab gerade meint. */
  feld: 'laenge' | 'winkel';
}

/** Eine frisch geöffnete Eingabe — die Ziffer, die sie geöffnet hat, steht schon drin. */
export function oeffneMit(ziffer: string): MassEingabe {
  return { laenge: ziffer, winkel: '', feld: 'laenge' };
}

/** Tab wechselt das Feld — und nur das. */
export function wechsleFeld(e: MassEingabe): MassEingabe {
  return { ...e, feld: e.feld === 'laenge' ? 'winkel' : 'laenge' };
}

/** Eine weitere Ziffer landet im gerade gemeinten Feld. */
export function tippe(e: MassEingabe, ziffer: string): MassEingabe {
  return e.feld === 'laenge'
    ? { ...e, laenge: e.laenge + ziffer }
    : { ...e, winkel: e.winkel + ziffer };
}

/**
 * **Was in der Fussfläche steht, während getippt wird.** Rein und damit prüfbar.
 *
 * *Der Satz nennt beide Ausgänge* — Enter und Escape — weil eine offene Eingabe sonst wie eine
 * Sackgasse aussieht. Dieselbe Regel wie beim Konturzug.
 */
export function massEingabeText(e: MassEingabe | null): string {
  if (!e) {
    return '';
  }
  const laenge = e.laenge === '' ? '…' : e.laenge;
  const winkel = e.winkel === '' ? '' : ` · ${e.winkel}°`;
  const gemeint = e.feld === 'laenge' ? 'Länge' : 'Winkel';

  return `${laenge} mm${winkel} · ${gemeint} · Tab wechselt · Enter setzt · Esc verwirft die Eingabe`;
}
