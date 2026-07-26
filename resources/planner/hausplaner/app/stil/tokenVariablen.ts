/**
 * AUF-38 Scheibe 1 — **die Tokens werden CSS-Variablen, erzeugt aus `T`.**
 *
 * **Nicht abgeschrieben, sondern abgeleitet.** Jede `--hp-<token>`-Variable traegt den Wert, der in
 * `studioDaten.ts` steht. Damit bleibt `T` die **einzige** Farbwahrheit; ein zweiter Farbwert in
 * der CSS waere genau die zweite Wahrheit, die T1 beseitigt hat — und er altert dort still, weil
 * niemand ihn nachfuehrt.
 *
 * **Rein bis auf den letzten Schritt:** `tokenVariablen()` rechnet, `setzeTokenVariablen()` setzt.
 * So ist die Ableitung ohne DOM pruefbar, und der Testlauf braucht kein Fenster.
 *
 * **Was hier NICHT passiert:** keine Umstellung. Scheibe 1 legt den Boden und ruehrt keine einzige
 * `style={{`-Stelle an.
 */
import { T } from '../studioDaten';

/** Das Praefix aller erzeugten Variablen. Eine Stelle — sonst entstuenden zwei Schreibweisen. */
export const HP_PRAEFIX = '--hp-';

/** Aus `camelCase` wird `kebab-case`: `accentSoft` ⇒ `--hp-accent-soft`. */
export function variablenName(token: string): string {
  return HP_PRAEFIX + token.replace(/([a-z0-9])([A-Z])/g, '$1-$2').toLowerCase();
}

/**
 * Alle Variablen als Paare. **Genau die Tokens aus `T`** — keiner mehr, keiner weniger.
 */
export function tokenVariablen(): Array<[string, string]> {
  return Object.entries(T).map(([name, wert]) => [variablenName(name), String(wert)]);
}

/**
 * Setzt die Variablen auf ein Element (im Betrieb: das Wurzelelement).
 *
 * **Ohne DOM tut sie nichts** statt zu werfen: der Testlauf hat kein Fenster, und ein Wurf dort
 * waere ein Fehler ueber eine Lage, die kein Fehler ist.
 */
export function setzeTokenVariablen(ziel?: { style: { setProperty(name: string, wert: string): void } } | null): void {
  const el = ziel ?? (typeof document !== 'undefined' ? document.documentElement : null);
  if (!el) {
    return;
  }
  for (const [name, wert] of tokenVariablen()) {
    el.style.setProperty(name, wert);
  }
}
