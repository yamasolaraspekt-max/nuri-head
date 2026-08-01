/**
 * Z-01 — **wann ein Werkzeug endet, und wann es nur pausiert.**
 *
 * ---
 *
 * **Der Befund, der dazu geführt hat.** Das Aufräumen beim Werkzeugwechsel stand **fünfmal**
 * abgeschrieben in zwei Dateien, und an einer sechsten Stelle — dem Rückfall auf `auswahl`, wenn
 * ein Werkzeug im aktuellen Bereich nicht mehr aktivierbar ist — **fehlte es ganz**. Fünf Kopien
 * einer Regel sind keine Regel; sie sind fünf Gelegenheiten, sie zu vergessen. *Einmal ist sie
 * vergessen worden.*
 *
 * **Was Schritt 0 im Browser gezeigt hat** (`docs/browsertest-z01-2026-07-31.md`): der
 * Vorschaustrich **folgt dem Zeiger nicht** aus der Fläche hinaus — `onMouseMove` hängt an der
 * Bühne, und draußen kommt kein Ereignis mehr an. **Er bleibt stehen**, dort wo der Zeiger die
 * Fläche zuletzt berührt hat:
 *
 * ```text
 * langsam nach oben hinaus    1500,1400 -> 2930, 3877
 * langsam nach rechts hinaus  1500,1400 -> 4200, 1400
 * ```
 *
 * *Das ist der „lange Strich": nicht ein Strich, der folgt, sondern einer, der liegen bleibt —
 * quer über den halben Grundriss, ohne dass irgendetwas sagt, dass die Aktion noch läuft.*
 *
 * ---
 *
 * **Die Festlegung, die dieses Modul trägt** (Yamas Standardempfehlung, vom Planner übernommen):
 *
 * | Ereignis | Teilaktion | Vorschau |
 * |---|---|---|
 * | **Werkzeugwechsel** | wird verworfen | fort |
 * | **Zeiger verlässt die Fläche** | **bleibt** | wird **ausgeblendet**, nicht eingefroren |
 * | **Zeiger kehrt zurück** | unverändert | lebt wieder auf, ohne Klick |
 *
 * **Der Unterschied zwischen ausblenden und einfrieren ist der ganze Auftrag.** Eingefroren sieht
 * aus wie ein gezeichnetes Bauteil; ausgeblendet sieht aus wie das, was es ist — eine Pause.
 *
 * ---
 *
 * **Was hier bewusst NICHT steht:** keine Lebenslauf-Schnittstelle mit zwölf Methoden (Yamas
 * Abschnitt 8 vollständig). Heute haben **zwei** Werkzeuge einen Zwischenzustand — Wand und Treppe.
 * Zwölf Methoden für zwei Nutzer wären ein Bauteil, das auf nichts zeigt. *Kommt mit dem
 * Polygonwerkzeug ein drittes, wird aus diesem Modul die Schnittstelle — getragen von drei
 * Nutzern statt von keinem.*
 *
 * **Und kein Anschluss an `fangKern`** — das ist Z-02 und hängt an dieser Scheibe, weil ein
 * Fangzustand erst gelöscht werden kann, wenn es einen Ort gibt, der löscht.
 */

/** Ein Punkt in Weltkoordinaten (mm). Bewusst lokal gehalten: dieses Modul kennt keine Geometrie. */
export interface StartPunkt {
  x: number;
  y: number;
}

/**
 * Der Zwischenzustand des Zeichnens. **Mehr braucht die Entscheidung nicht** — deshalb steht hier
 * weder das Werkzeug noch die Zeigerposition.
 */
export interface ZeichenZustand {
  /** Gesetzter Anfangspunkt einer Wand, oder `null`. */
  wandStart: StartPunkt | null;
  /** Gesetzter Anfangspunkt einer Treppen-Lauflinie, oder `null`. */
  treppeStart: StartPunkt | null;
  /** Ist der Zeiger auf der Zeichenfläche? */
  zeigerDrinnen: boolean;
}

/** Nichts angefangen, Zeiger auf der Fläche. */
export const ZEICHEN_LEER: ZeichenZustand = { wandStart: null, treppeStart: null, zeigerDrinnen: true };

/**
 * **Der Werkzeugwechsel bricht ab.** Eine unbestätigte Teilaktion gehört dem alten Werkzeug; sie
 * mit ins neue zu nehmen wäre die Ursache des Reststrichs.
 *
 * *`zeigerDrinnen` bleibt, wie es war* — wo der Zeiger steht, ändert sich durch einen Werkzeugwechsel
 * nicht. **Genau das war der Fehler in der alten Fassung:** vier der fünf Kopien setzten die
 * Startpunkte, sagten aber nichts über den Zeiger, und die fünfte (der Rückfall) setzte gar nichts.
 */
export function beiWerkzeugwechsel(z: ZeichenZustand): ZeichenZustand {
  return { wandStart: null, treppeStart: null, zeigerDrinnen: z.zeigerDrinnen };
}

/**
 * **Das Verlassen der Fläche pausiert nur.** Der Anfangspunkt bleibt stehen — wer zur Werkzeugleiste
 * fährt, um den Fang umzuschalten, will seinen Zug nicht verlieren.
 */
export function beiZeigerAus(z: ZeichenZustand): ZeichenZustand {
  return { ...z, zeigerDrinnen: false };
}

/** **Zurück auf der Fläche:** die Vorschau lebt wieder auf, ohne Klick. */
export function beiZeigerEin(z: ZeichenZustand): ZeichenZustand {
  return { ...z, zeigerDrinnen: true };
}

/** Läuft gerade ein Zug, der noch nicht bestätigt ist? */
export function zugLaeuft(z: ZeichenZustand): boolean {
  return z.wandStart !== null || z.treppeStart !== null;
}

/**
 * **Wird die Vorschau gezeichnet?** Zwei Bedingungen, und die zweite ist die neue: es muss ein
 * Anfangspunkt gesetzt sein **und** der Zeiger auf der Fläche stehen.
 *
 * *Ohne die zweite Bedingung bleibt die Linie beim Verlassen stehen — genau das, was Schritt 0
 * gemessen hat.*
 */
export function zeigtVorschau(z: ZeichenZustand, art: 'wand' | 'treppe'): boolean {
  if (!z.zeigerDrinnen) return false;
  return art === 'wand' ? z.wandStart !== null : z.treppeStart !== null;
}

/**
 * **Der Satz für die Statusleiste** — oder `null`, wenn nichts zu sagen ist.
 *
 * Er erscheint **nur**, wenn wirklich etwas pausiert: Zeiger draußen **und** ein Zug angefangen.
 * *Ein Hinweis, der immer dasteht, wird nicht gelesen.*
 */
export const PAUSEN_TEXT = 'Zeichnung pausiert — zurück auf die Fläche setzt fort, Esc bricht ab';

export function pausenText(z: ZeichenZustand): string | null {
  if (z.zeigerDrinnen) return null;
  return zugLaeuft(z) ? PAUSEN_TEXT : null;
}
