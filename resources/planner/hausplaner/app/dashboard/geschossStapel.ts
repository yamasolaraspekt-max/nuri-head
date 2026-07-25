/**
 * AUF-43 — der **Geschoss-Stapel** als Daten.
 *
 * **Der gemessene Befund:** dreizehn Bedienelemente in einer Zeile, vier voneinander unabhängige
 * Aufgaben, und der Geschossname stand **zweimal nebeneinander** — einmal als 111-px-Select, einmal
 * als Textfeld mit demselben Wert. Die Höhenlage (`elevation`) wird im Modell geführt, aber nirgends
 * gezeigt. Und es gab **kein Bild vom Stapel**: der Nutzer sah nie, wie viele Geschosse
 * übereinanderliegen und wo er gerade ist.
 *
 * **Warum ein Datenmodul:** „drei Geschosse, das mittlere ist aktiv, darüber liegt eines" ist eine
 * **Aussage über das Modell** — die gehört geprüft, nicht angesehen. Dieselbe Entscheidung wie bei
 * `werkzeugGruppen`, `projektBaum` und `fahrschritte`.
 *
 * **Die Ordnung bleibt, wie sie ist:** `sortOrder`, dann `elevation`. Der Auftrag verbietet die
 * Sortierumkehr ausdrücklich — hier wird nur **angezeigt**, was ohnehin gilt.
 *
 * **Kein neuer Zustand.** Diese Datei liest `levels` und die aktive id; welches Geschoss aktiv ist,
 * weiß allein `setActiveLevel` im Store.
 */
import type { Level } from '../../domain/scene.types';

export interface StapelEintrag {
  id: string;
  name: string;
  /** Rohwert aus dem Modell, mm. */
  elevation: number;
  /** Die Höhenlage, wie ein Architekt sie liest: `±0 mm`, `+2 700 mm`, `−2 800 mm`. */
  hoehenLabel: string;
  aktiv: boolean;
  /** 1-basiert von unten — die Zahl, die im Stapelbild steht. */
  position: number;
}

export interface Stapel {
  /** **Von oben nach unten** — so, wie ein Gebäudeschnitt gelesen wird. */
  eintraege: StapelEintrag[];
  anzahl: number;
  /** 1-basierte Position des aktiven Geschosses von unten; 0, wenn keines aktiv ist. */
  aktivPosition: number;
  /** Wie viele liegen über bzw. unter dem aktiven. */
  darueber: number;
  darunter: number;
  aktiv: StapelEintrag | null;
}

/**
 * Höhenlage als Text. **Tausendertrennung mit schmalem Leerzeichen**, Vorzeichen explizit — `+2 700 mm`
 * ist auf einen Blick als Höhe über null lesbar, `2700` nicht. `±0 mm` statt `+0 mm`, weil das
 * Erdgeschoss keine Richtung hat.
 */
export function hoehenLabel(elevation: number): string {
  if (elevation === 0) return '±0 mm';
  const zeichen = elevation > 0 ? '+' : '−';
  // U+202F (schmales geschütztes Leerzeichen) als Escape geschrieben — als sichtbares Zeichen
  // wäre es von einem gewöhnlichen Leerzeichen nicht zu unterscheiden, und genau daran ist
  // mein erster Test gescheitert. Geschützt, damit die Zahl nicht mitten im Wert umbricht.
  const betrag = Math.abs(elevation).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '\u202f');
  return `${zeichen}${betrag} mm`;
}

/**
 * Der Stapel aus den Geschossen des Dokuments. **Rein** — nimmt Daten, gibt Daten, verändert nichts.
 * Unbekannte aktive id ⇒ `aktiv: null` und `aktivPosition: 0`, kein Wurf: eine Auswahl darf einem
 * gelöschten Geschoss folgen, ohne die Fläche mitzureißen.
 */
export function stapel(levels: readonly Level[], aktivId: string | null): Stapel {
  const vonUnten = [...levels].sort((a, b) => a.sortOrder - b.sortOrder || a.elevation - b.elevation);
  const eintraege = vonUnten.map((l, i) => ({
    id: l.id,
    name: l.name,
    elevation: l.elevation,
    hoehenLabel: hoehenLabel(l.elevation),
    aktiv: l.id === aktivId,
    position: i + 1,
  }));
  const aktivIndex = eintraege.findIndex((e) => e.aktiv);
  const aktiv = aktivIndex >= 0 ? eintraege[aktivIndex] : null;
  return {
    // Von oben nach unten anzeigen; die Positionsnummer zählt weiter von unten.
    eintraege: [...eintraege].reverse(),
    anzahl: eintraege.length,
    aktivPosition: aktiv ? aktiv.position : 0,
    darueber: aktivIndex >= 0 ? eintraege.length - 1 - aktivIndex : 0,
    darunter: aktivIndex >= 0 ? aktivIndex : 0,
    aktiv,
  };
}

/**
 * Kurzfassung für den geschlossenen Zustand: `Erdgeschoss · ±0 mm · 1 von 3`. Sie steht auf dem
 * Knopf, der die Fläche öffnet — damit die wichtigste Angabe sichtbar bleibt, ohne die Zeile mit
 * dreizehn Elementen zu füllen.
 */
export function kurzfassung(s: Stapel): string {
  if (!s.aktiv) return `${s.anzahl} Geschosse`;
  return `${s.aktiv.name} · ${s.aktiv.hoehenLabel} · ${s.aktivPosition} von ${s.anzahl}`;
}

/** Das Nachbargeschoss in Richtung `+1` (darüber) oder `−1` (darunter) — oder `undefined`. */
export function nachbar(s: Stapel, richtung: 1 | -1): StapelEintrag | undefined {
  if (!s.aktiv) return undefined;
  const vonUnten = [...s.eintraege].reverse();
  return vonUnten[s.aktiv.position - 1 + richtung];
}
