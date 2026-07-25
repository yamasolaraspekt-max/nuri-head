/**
 * AUF-45 — **der erste Schritt**, aus den vorhandenen Sperrgründen gezählt.
 *
 * **Der Befund:** Beim ersten Öffnen ist der größte Teil der Werkzeuge gesperrt — und **jede
 * einzelne Sperre ist korrekt** (AUF-36 hat den Funktionsvertrag eingehängt, jedes Werkzeug nennt
 * seinen Grund). Der Defekt ist, dass die Oberfläche **schweigt**: niemand erfährt, dass ein
 * einziger Handgriff — ein Geschoss anlegen — auf einen Schlag ein Fünftel des Werkzeugkastens
 * freischaltet. Die Information liegt vollständig vor; sie wird nur nie zusammengefasst.
 *
 * ## Keine zweite Aktivierungs-Engine
 *
 * Dieses Modul wertet **keine** Vorbedingung aus. Es bekommt die Zustände, die `resolveToolState`
 * ohnehin geliefert hat, **zählt** sie nach ihrem Grund und **vergleicht** zwei Zustandslisten.
 * Mehr nicht. Wer hier eine eigene Regel „was ist der erste Schritt" schriebe, hätte genau die
 * zweite Engine gebaut, die AUF-36 §3(a) verbietet.
 *
 * ## Nicht der häufigste Grund gewinnt, sondern der, der wirklich etwas löst
 *
 * **Gemessen, und es widerlegt die Annahme des Auftrags:** Im leeren Plan ist der häufigste
 * Sperrgrund *„Dafür muss zuerst etwas ausgewählt sein"* (23 Werkzeuge) — vor *„Kein aktives
 * Geschoss"* (22). Als erster Schritt wäre „wähle etwas aus" jedoch **unbrauchbar**: in einem leeren
 * Plan gibt es nichts auszuwählen. Die reine Häufigkeit ist also der falsche Maßstab.
 *
 * **Der richtige ist messbar, ohne eine einzige eigene Regel:** Für jeden Kandidaten fragt der
 * Aufrufer dieselbe Engine ein zweites Mal — „wie sähe es aus, wenn diese Vorbedingung erfüllt
 * wäre?" — und dieses Modul **zählt die Differenz**. Gewinner ist, wer am meisten freischaltet.
 * Ein Schritt, der nichts löst, kann so gar nicht gewinnen.
 *
 * **Die Zahl im Satz ist die echte:** im leeren Plan **warten** 22 Werkzeuge auf ein Geschoss, aber
 * nur **20** werden dadurch bedienbar — zwei bleiben aus einem anderen Grund gesperrt. „Schaltet 22
 * frei" wäre falsch. Ohne messbaren Kandidaten sagt der Wegweiser „warten", nicht „schaltet frei".
 */
import type { WerkzeugZustand } from './toolTypes';

export interface Wegweiser {
  /** Der Sperrgrund im Wortlaut, wie ihn `resolveToolState` geliefert hat. */
  grund: string;
  /** Wie viele Werkzeuge dieser Grund gerade sperrt. */
  wartend: number;
  /**
   * Wie viele davon durch den Schritt **wirklich** bedienbar werden. `null`, wenn der Vergleich
   * nicht möglich war — dann wird „warten" gesagt, nicht „wird freigeschaltet".
   */
  entsperrt: number | null;
}

/** Zählt die Sperrgründe. Nur gesperrte Zustände mit Grund zählen. */
export function gruende(zustaende: readonly WerkzeugZustand[]): Map<string, number> {
  const m = new Map<string, number>();
  for (const z of zustaende) {
    if (z.enabled || !z.reason) continue;
    m.set(z.reason, (m.get(z.reason) ?? 0) + 1);
  }
  return m;
}

/** Ein prüfbarer Kandidat: ein Sperrgrund und die Zustände, die nach seiner Erfüllung gälten. */
export interface Kandidat {
  grund: string;
  /** Dieselben Werkzeuge, von derselben Engine im hypothetischen Kontext bewertet. */
  danach: readonly WerkzeugZustand[];
}

/**
 * Der nächste Schritt — oder `null`, wenn nichts gesperrt ist.
 *
 * **Mit Kandidaten** gewinnt der, dessen Erfüllung die **meisten** Werkzeuge entsperrt (gemessen,
 * nicht geschätzt). Löst keiner etwas, gibt es keinen Wegweiser: ein Ratschlag ohne Wirkung ist
 * schlechter als Schweigen.
 *
 * **Ohne Kandidaten** bleibt nur die Häufigkeit — dann steht „warten" im Satz, nicht „schaltet frei".
 * **Gleichstand** entscheidet alphabetisch: ohne feste Regel entschiede die Reihenfolge der
 * Werkzeugliste, und der Wegweiser spränge bei jeder Katalog-Änderung.
 */
export function naechsterSchritt(
  jetzt: readonly WerkzeugZustand[],
  kandidaten: readonly Kandidat[] = [],
): Wegweiser | null {
  const zaehler = gruende(jetzt);
  if (zaehler.size === 0) return null;
  const gesperrtJetzt = jetzt.filter((z) => !z.enabled).length;

  const gemessen = kandidaten
    .map((k) => ({ grund: k.grund, entsperrt: gesperrtJetzt - k.danach.filter((z) => !z.enabled).length }))
    .filter((k) => k.entsperrt > 0)
    .sort((a, b) => b.entsperrt - a.entsperrt || a.grund.localeCompare(b.grund));

  if (gemessen.length > 0) {
    const beste = gemessen[0];
    return { grund: beste.grund, wartend: zaehler.get(beste.grund) ?? 0, entsperrt: beste.entsperrt };
  }
  if (kandidaten.length > 0) return null;

  const [grund, wartend] = [...zaehler].sort((a, b) => b[1] - a[1] || a[0].localeCompare(b[0]))[0];
  return { grund, wartend, entsperrt: null };
}

/**
 * Der Satz, der in der Oberfläche steht. **Getrennt von der Zählung**, damit die Formulierung
 * geprüft werden kann, ohne die Zahlen nachzubauen.
 *
 * `handlung` ist die Aufforderung in Klartext („Lege ein Geschoss an"); sie kommt aus der
 * Vorbedingungs-Tabelle, nicht aus diesem Modul.
 */
export function wegweiserSatz(w: Wegweiser, handlung: string): string {
  return w.entsperrt !== null
    ? `${handlung} — das schaltet ${w.entsperrt} ${w.entsperrt === 1 ? 'Werkzeug' : 'Werkzeuge'} frei.`
    : `${handlung} — darauf warten ${w.wartend} ${w.wartend === 1 ? 'Werkzeug' : 'Werkzeuge'}.`;
}
