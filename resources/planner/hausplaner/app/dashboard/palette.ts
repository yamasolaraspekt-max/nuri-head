/**
 * Dashboard v2.5 (§30 / UI-9) — Command-Palette als DATEN.
 *
 * Reine Funktion, kein React. `enabled`/`grund` kommen AUSSCHLIESSLICH aus `resolveToolState`. Es
 * gibt hier bewusst KEINE zweite Aktivierungslogik — wäre eine da, könnte die Palette ein Werkzeug
 * anbieten, das die Werkzeugleiste sperrt (oder umgekehrt). Der Gegen-Beweis zu Kriterium 9 hängt
 * genau an dieser Zeile.
 *
 * Deaktivierte Einträge tragen ihren `grund` als sichtbaren TEXT (nicht nur ausgegraut, §28 /
 * WCAG 1.4.1) und sind nicht auslösbar.
 *
 * ---
 *
 * **AUF-67 — die Palette wird globale Navigation.**
 *
 * **Der Befund:** sie konnte genau **eine** Art von Sache — sie fand Werkzeuge. Alles andere, wonach
 * jemand sucht (ein Geschoss, ein Bauteil, ein Arbeitsbereich, der nächste Schritt), existierte
 * bereits als **Register**, und die Palette fragte keines davon.
 *
 * **Die eiserne Regel dieses Postens:**
 *
 * > **Die Palette weiß nichts selbst. Sie fragt die vorhandenen Register.**
 *
 * Für jede Art gibt es **genau eine** Quelle, und es ist die, die die Oberfläche ohnehin benutzt:
 * Geschosse aus `geschossStapel`, Bauteile aus `projektBaum`, Bereiche aus `arbeitsbereiche`, der
 * Schritt aus `naechsterSchritt`, Werkzeuge aus der Registry. **Die Register werden hier nicht
 * erneut gerechnet, sondern als fertiges Ergebnis hereingereicht** — dasselbe Ergebnis, das die
 * Oberfläche anzeigt. Wer hier eine eigene Liste anlegte, baute die zweite Wahrheit, die diese
 * Datei ausdrücklich vermeidet.
 *
 * **Die Palette führt hin; sie erfindet nichts.** Jede Art bildet auf eine Handlung ab, die es ohne
 * sie auch gäbe: Werkzeug wählen, Geschoss wechseln, Bauteil auswählen, Bereich wechseln.
 */
import { alleTools } from '../tools/toolRegistry';
import { resolveToolState } from '../tools/activation';
import type { AktivierungsKontext } from '../tools/toolTypes';
import { ARBEITSBEREICHE } from './arbeitsbereiche';
import type { Stapel } from './geschossStapel';
import type { BaumGruppe } from './projektBaum';

/** Die Arten, die die Palette findet. Mehr gibt es nicht — und jede hat genau ein Register. */
export type PaletteArt = 'werkzeug' | 'geschoss' | 'bauteil' | 'bereich' | 'schritt';

export interface PaletteEintrag {
  /** AUF-67: woher der Eintrag stammt — und damit, wohin ein Klick führt. */
  art: PaletteArt;
  id: string;
  label: string;
  shortcut?: string;
  enabled: boolean;
  /** Grund der Deaktivierung, sonst `null`. Kommt aus `resolveToolState`, nie aus dieser Datei. */
  grund: string | null;
  /**
   * Unterscheidung bei gleichem Namen — Höhenlage beim Geschoss, Gruppe beim Bauteil. **Aus dem
   * Register gelesen, nicht hier gebildet.** Drei Zeilen „Wand" ohne Zusatz sind drei Zeilen, die
   * man nicht auseinanderhalten kann.
   */
  zusatz?: string;
}

export interface PaletteGruppe {
  art: PaletteArt;
  titel: string;
  eintraege: PaletteEintrag[];
  /** Wörtlich, wenn diese Art nichts beisteuert. **Kein leerer Kasten.** */
  leer: string;
}

/** Leerzustand bei Filter ohne Treffer — wörtlich (Kante 7). Kein leerer Kasten. */
export const PALETTE_LEER = 'Kein Werkzeug passt zu dieser Eingabe.';

/**
 * Die Gruppen in **fester** Anzeigereihenfolge, mit Titel und wörtlichem Leerzustand.
 *
 * Fest, weil eine Palette, deren Abschnitte je nach Trefferlage die Plätze tauschen, das Laufen mit
 * den Pfeiltasten unbrauchbar macht: man zielt auf die dritte Zeile und trifft etwas anderes.
 */
export const PALETTE_ARTEN: ReadonlyArray<{ art: PaletteArt; titel: string; leer: string }> = [
  { art: 'werkzeug', titel: 'Werkzeuge', leer: PALETTE_LEER },
  { art: 'geschoss', titel: 'Geschosse', leer: 'Kein Geschoss passt zu dieser Eingabe.' },
  { art: 'bauteil', titel: 'Bauteile', leer: 'Kein Bauteil in diesem Geschoss passt zu dieser Eingabe.' },
  { art: 'bereich', titel: 'Arbeitsbereiche', leer: 'Kein Arbeitsbereich passt zu dieser Eingabe.' },
  { art: 'schritt', titel: 'Nächster Schritt', leer: 'Gerade ist kein nächster Schritt offen.' },
];

/**
 * Was die Palette liest. **Alles fertig gerechnet** — die Register laufen in der Oberfläche ohnehin;
 * sie hier ein zweites Mal aufzurufen wäre eine zweite Rechnung über dieselbe Sache.
 */
export interface PaletteQuellen {
  /** Derselbe Kontext, den auch die Werkzeugleiste benutzt. */
  kontext: AktivierungsKontext;
  /** Ergebnis von `stapel(levels, aktivId)`. */
  stapel?: Stapel | null;
  /** Ergebnis von `projektBaum(nodes, roofs, level)`. */
  baum?: readonly BaumGruppe[];
  /** Ergebnis des Wegweisers, wie die Oberfläche es schon zeigt. */
  schritt?: { satz: string; ort: string } | null;
}

const passt = (f: string, ...felder: Array<string | undefined>): boolean =>
  f === '' || felder.some((s) => (s ?? '').toLowerCase().includes(f));

/**
 * Einträge der Palette für den aktuellen Kontext — **nur Werkzeuge**.
 *
 * Bleibt unverändert erhalten: die Werkzeug-Auflösung ist dieselbe wie vor AUF-67.
 *
 * @param kontext Aktivierungs-Kontext (aus `baueAktivierungsKontext`) — derselbe, den auch die
 *                Werkzeugleiste benutzt.
 * @param filter  Freitext; trifft `label` UND `id`, ohne Groß-/Kleinschreibung. Leer ⇒ alle.
 */
export function palettenEintraege(kontext: AktivierungsKontext, filter: string): PaletteEintrag[] {
  const f = filter.trim().toLowerCase();
  const eintraege: PaletteEintrag[] = alleTools()
    .filter((t) => passt(f, t.label, t.id))
    .map((t) => {
      const zustand = resolveToolState(t, kontext);
      return {
        art: 'werkzeug' as const,
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

/**
 * **Alle Arten, gruppiert.** Innerhalb einer Art bleibt die Reihenfolge des Registers erhalten —
 * nichts springt, wenn sich die Auswahl ändert.
 *
 * **Navigations-Einträge sind immer `enabled`:** sie *führen hin*, und dorthin zu führen ist nie
 * gesperrt. Nur Werkzeuge können einen Grund tragen, und der kommt aus `resolveToolState`.
 */
export function palettenGruppen(quellen: PaletteQuellen, filter: string): PaletteGruppe[] {
  const f = filter.trim().toLowerCase();

  const werkzeuge = palettenEintraege(quellen.kontext, filter);

  // Geschosse: Reihenfolge des Stapels — von oben nach unten, so wird ein Gebäudeschnitt gelesen.
  const geschosse: PaletteEintrag[] = (quellen.stapel?.eintraege ?? [])
    .filter((g) => passt(f, g.name, g.id, g.hoehenLabel))
    .map((g) => ({
      art: 'geschoss' as const, id: g.id, label: g.name,
      enabled: true, grund: null, zusatz: g.hoehenLabel,
    }));

  // Bauteile: Gruppenreihenfolge des Projektbaums, innerhalb der Gruppe seine Reihenfolge.
  const bauteile: PaletteEintrag[] = (quellen.baum ?? []).flatMap((gruppe) =>
    gruppe.eintraege
      .filter((b) => passt(f, b.label, b.id, gruppe.gruppe))
      .map((b) => ({
        art: 'bauteil' as const, id: b.id, label: b.label,
        enabled: true, grund: null, zusatz: gruppe.gruppe,
      })));

  const bereiche: PaletteEintrag[] = ARBEITSBEREICHE
    .filter((b) => passt(f, b.label, b.id))
    .map((b) => ({
      art: 'bereich' as const, id: b.id, label: b.label,
      enabled: true, grund: null, zusatz: b.hinweis,
    }));

  // Der Wegweiser liefert **höchstens einen** Schritt — mehr weiß das Register nicht, und mehr wird
  // hier nicht erfunden.
  const schritt: PaletteEintrag[] = quellen.schritt && passt(f, quellen.schritt.satz)
    ? [{
      art: 'schritt' as const, id: quellen.schritt.ort, label: quellen.schritt.satz,
      enabled: true, grund: null,
    }]
    : [];

  const je: Record<PaletteArt, PaletteEintrag[]> = {
    werkzeug: werkzeuge, geschoss: geschosse, bauteil: bauteile, bereich: bereiche, schritt,
  };
  return PALETTE_ARTEN.map((a) => ({ art: a.art, titel: a.titel, eintraege: je[a.art], leer: a.leer }));
}

/**
 * Die Einträge aller Gruppen hintereinander — für das Laufen mit den Pfeiltasten.
 *
 * **Eine Liste, zwei Darstellungen:** die Tastatur läuft über dieselbe Reihenfolge, die das Auge
 * sieht. Zwei getrennte Reihenfolgen wären die zweite Wahrheit im Kleinen.
 */
export function palettenFlach(gruppen: readonly PaletteGruppe[]): PaletteEintrag[] {
  return gruppen.flatMap((g) => g.eintraege);
}
