/**
 * AUF-83-T5 / K-03 — die Rangfolge selbst: rein, ohne DOM prüfbar.
 *
 * Das eigentliche Verhalten (welcher `document`-Listener bei welchem Tastendruck reagiert) prüft
 * `escapeStapel.dom.test.ts` — dort gibt es echte Ereignisse. Hier steht nur die Regel, die
 * `oberste` trifft: reiner Code, ohne Browser messbar, dieselbe Trennung wie
 * `dialogFokus.naechsterIndex`.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';
import { RANGFOLGE, oberste, type EscapeEbenenArt } from '../app/dashboard/escapeStapel';

const hier = dirname(fileURLToPath(import.meta.url));

const eintrag = (id: number, art: EscapeEbenenArt): { id: number; art: EscapeEbenenArt } => ({ id, art });

test('K-03: die Rangfolge ist wörtlich Yamas Satz — Palette > Dialog > Menue > Schiene > Werkzeug-Reset', () => {
  assert.deepEqual(RANGFOLGE, ['palette', 'dialog', 'menue', 'schiene', 'werkzeug-reset']);
});

test('K-03: die oberste aktive Ebene ist die mit dem höchsten Rang, nicht die zuletzt registrierte', () => {
  // Werkzeug-Reset zuerst registriert, Palette danach — die Palette gewinnt trotzdem.
  const liste = [eintrag(1, 'werkzeug-reset'), eintrag(2, 'palette')];
  assert.equal(oberste(liste)?.id, 2);
});

test('K-03: Palette schlägt Dialog schlägt Menü schlägt Schiene schlägt Werkzeug-Reset — jede Paarung', () => {
  const paare: Array<[EscapeEbenenArt, EscapeEbenenArt]> = [
    ['palette', 'dialog'], ['dialog', 'menue'], ['menue', 'schiene'], ['schiene', 'werkzeug-reset'],
  ];
  for (const [oben, unten] of paare) {
    const liste = [eintrag(1, unten), eintrag(2, oben)];
    assert.equal(oberste(liste)?.art, oben, `${oben} sollte ${unten} schlagen`);
  }
});

test('K-03: bei Gleichstand gewinnt die zuletzt registrierte — ein literaler Stapel', () => {
  const liste = [eintrag(1, 'menue'), eintrag(2, 'menue'), eintrag(3, 'menue')];
  assert.equal(oberste(liste)?.id, 3, 'die zuletzt geöffnete Ebene sollte zuerst schließen');
});

test('K-03: eine leere Liste hat keine oberste Ebene', () => {
  assert.equal(oberste([]), undefined);
});

// --- Der reale Fehler, gefunden bei der Sichtprobe, nicht von einem Gate -----------------------

test('K-03 (Befund T5-K03-B1): die Palette schließt sich NUR über den Stapel — kein zweiter, direkter Weg mehr', () => {
  // **Der Fehler, live im Browser gefunden.** Das Filterfeld der Palette hatte einen EIGENEN
  // `onKeyDown`-Zweig, der bei Escape `schliessePalette()` DIREKT aufrief — parallel zum
  // Stapel-Eintrag `useEscapeEbene('palette', …)`. Das löste beim Tippen im fokussierten Feld
  // einen React-Render aus, der GeschossFlaeches Stapel-Eintrag AB- und NEU ANMELDETE, bevor der
  // Stapel-Listener selbst zum Zug kam — die Palette war dann schon aus der Rangliste verschwunden,
  // und das Menü (rangniedriger) gewann fälschlich mit. **Ergebnis: ein Escape schloss BEIDE**,
  // genau der Fehler, den dieser Auftrag beheben sollte.
  //
  // Ein DOM-Test kann diese React-interne Zeiteinteilung nicht zuverlässig nachstellen (die Sache
  // hängt an der genauen Reihenfolge von Render/Effekt-Flush, nicht an einer reinen Funktion) —
  // deshalb steht der Beleg hier als Quelltext-Zusage: das Filterfeld darf `schliessePalette`
  // nicht mehr direkt im `onKeyDown` aufrufen. Gefunden mit einem echten Browser (Puppeteer,
  // Objekt 203, Palette + Geschoss-Menü gleichzeitig offen, Escape gedrückt), nicht am Quelltext.
  const app = readFileSync(join(hier, '../app/HausplanerApp.tsx'), 'utf8');
  const zeilen = app.split('\n').filter((z) => !z.trim().startsWith('*') && !z.trim().startsWith('//'));
  const direkterAufruf = zeilen.filter((z) => z.includes("'Escape'") && z.includes('schliessePalette()'));
  assert.deepEqual(direkterAufruf, [],
    `das Filterfeld schließt die Palette wieder direkt, nicht über den Stapel:\n${direkterAufruf.join('\n')}`);
  // presence-Partner (R2): der Stapel-Eintrag existiert wirklich — sonst prüfte die Zusage oben Leere.
  assert.match(app, /useEscapeEbene\('palette', paletteOffen, schliessePalette\)/,
    'ohne den Stapel-Eintrag schlösse die Palette auf GAR KEIN Escape mehr');
});

test('K-03: eine unbekannte Ebenen-Art landet ganz unten, nicht ganz oben', () => {
  // Rein defensiv — heute kann kein Aufrufer eine Art außerhalb `EscapeEbenenArt` übergeben
  // (TypeScript verbietet es), aber `rang()` selbst braucht einen definierten Fall für „nicht in
  // der Liste", falls die Rangfolge künftig wächst und ein Aufrufer nicht mitzieht.
  const liste = [eintrag(1, 'menue'), eintrag(2, 'unbekannt' as EscapeEbenenArt)];
  assert.equal(oberste(liste)?.id, 1, 'eine unbekannte Art hat die bekannte verdrängt');
});
