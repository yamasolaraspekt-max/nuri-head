/**
 * Dashboard v2.5 (§30 / UI-9) — Command-Palette als DATEN.
 *
 * Reine Funktion, kein React: Quelle ist `alleTools()` aus der Tool-Registry (EINE Wahrheit über
 * die Werkzeuge), `enabled`/`grund` kommen AUSSCHLIESSLICH aus `resolveToolState`. Es gibt hier
 * bewusst KEINE zweite Aktivierungslogik — wäre eine da, könnte die Palette ein Werkzeug
 * anbieten, das die Werkzeugleiste sperrt (oder umgekehrt). Der Gegen-Beweis zu Kriterium 9
 * hängt genau an dieser Zeile.
 *
 * Reihenfolge: aktivierbare Einträge zuerst, deaktivierte darunter — INNERHALB beider Blöcke
 * bleibt die Registry-Reihenfolge erhalten. So springt nichts, wenn sich die Auswahl ändert.
 *
 * Deaktivierte Einträge tragen ihren `grund` als sichtbaren TEXT (nicht nur ausgegraut, §28 /
 * WCAG 1.4.1) und sind nicht auslösbar.
 */
import { alleTools } from '../tools/toolRegistry';
import { resolveToolState } from '../tools/activation';
import type { AktivierungsKontext } from '../tools/toolTypes';

export interface PaletteEintrag {
  id: string;
  label: string;
  shortcut?: string;
  enabled: boolean;
  /** Grund der Deaktivierung, sonst `null`. Kommt aus `resolveToolState`, nie aus dieser Datei. */
  grund: string | null;
}

/** Leerzustand bei Filter ohne Treffer — wörtlich (Kante 7). Kein leerer Kasten. */
export const PALETTE_LEER = 'Kein Werkzeug passt zu dieser Eingabe.';

/**
 * Einträge der Palette für den aktuellen Kontext.
 *
 * @param kontext Aktivierungs-Kontext (aus `baueAktivierungsKontext`) — derselbe, den auch die
 *                Werkzeugleiste benutzt.
 * @param filter  Freitext; trifft `label` UND `id`, ohne Groß-/Kleinschreibung. Leer ⇒ alle.
 */
export function palettenEintraege(kontext: AktivierungsKontext, filter: string): PaletteEintrag[] {
  const f = filter.trim().toLowerCase();
  const eintraege: PaletteEintrag[] = alleTools()
    .filter((t) => f === '' || t.label.toLowerCase().includes(f) || t.id.toLowerCase().includes(f))
    .map((t) => {
      const zustand = resolveToolState(t, kontext);
      return {
        id: t.id,
        label: t.label,
        shortcut: t.shortcut,
        enabled: zustand.enabled,
        grund: zustand.reason,
      };
    });
  // Stabile Zweiteilung: filter() erhält die Ursprungsreihenfolge (Registry) in beiden Blöcken.
  return [...eintraege.filter((e) => e.enabled), ...eintraege.filter((e) => !e.enabled)];
}
