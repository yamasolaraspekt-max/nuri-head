/**
 * AUF-91 — **die ehrliche Sperre unter 1024 px, verriegelt.**
 *
 * ---
 *
 * **Die Mutationsprobe VOR diesen Zusagen — 4 Mutationen, 4 kamen durch** (das Modul war neu):
 *
 * ```text
 * Hinweis erscheint IMMER · Schwelle im Text auf 800 · Rolle fuer Vorleseprogramme entfernt
 * aus der Vertagung wird eine Absage
 * ```
 *
 * ---
 *
 * **⚠ Befund gegen das Blatt, vor dem Bau gemessen und gemeldet.** K-02 schlägt in seiner
 * Begründung `buehnenBreite.ts` als Quelle der Schwelle vor (*„der Schalter existiert, er muss nur
 * gelesen werden"*). **Gemessen trägt diese Quelle die verlangte Schwelle nicht:**
 *
 * ```text
 * Fenster   Behaelter (inhaltRef)     matchMedia (max-width: 1023px)
 *   1440           1077                        false
 *   1100            737                        false
 *   1024            661                        false     <- Behaelter schon weit unter 1024
 *   1023            —                          true
 * ```
 *
 * *Eine Schwelle „Behälter < 1024" spränge bereits bei 1100 px Fensterbreite an — genau dort, wo
 * K-01 „unverändert, KEIN zusätzliches Element" verlangt.* **Der Schalter, der die verlangte
 * Schwelle wirklich trägt, stand schon in der Datei:** `useIstSchmal()`, seit AUF-83-T5 für das
 * Overlay-Verhalten der Schienen in Gebrauch. **Gelesen, nicht verdoppelt** — genau das, was
 * „kein zweiter Messweg" meint.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { MINDESTBREITE_PX } from '../app/rahmen/MindestbreiteHinweis';
import { teil, ohneKommentare } from './_zerlegteApp';

const hinweisRoh = teil('app/rahmen/MindestbreiteHinweis.tsx');
const hinweis = ohneKommentare(hinweisRoh);
const app = ohneKommentare(teil('app/HausplanerApp.tsx'));
const css = teil('hausplaner.css');

// --- K-01: die Schwelle, und dass sie überhaupt eine ist ------------------------------------------

test('K-01: über der Mindestbreite entsteht KEIN Element — die Fläche rendert `null`', () => {
  // Mutation „Hinweis erscheint IMMER" kam durch. Ein Overlay, das auf 1440 px über dem Plan
  // liegt, wäre schlimmer als der Mangel, den es meldet.
  assert.match(hinweis, /if \(!sichtbar\) return null;/,
    'die Fläche rendert auch über der Mindestbreite — dann liegt sie über dem Planer');
  // presence-Partner nach R2: sie rendert überhaupt etwas, wenn sie soll.
  assert.match(hinweis, /className="hp-mb-flaeche"/, 'die Fläche hat kein Markup — die Zusage misst Leere');
});

test('K-01: die Schwelle ist 1024 und kommt aus dem Schalter, den es schon gibt', () => {
  // Mutation „Schwelle im Text auf 800" kam durch: der Satz nennte eine Zahl, die mit dem
  // tatsächlichen Umschaltpunkt nichts zu tun hat.
  assert.equal(MINDESTBREITE_PX, 1024, 'die genannte Mindestbreite ist nicht mehr 1024');
  // Der Schalter selbst: `useIstSchmal` prüft `(max-width: 1023px)` — also genau 1024 als Grenze.
  assert.match(app, /max-width: 1023px/, 'der vorhandene Schalter prüft nicht mehr auf 1023 px');
  assert.match(app, /<MindestbreiteHinweis sichtbar=\{istSchmal\} \/>/,
    'die Fläche liest nicht mehr den vorhandenen Schalter — dann gibt es einen zweiten Messweg');
});

test('K-01 (Grenze): die Fläche baut KEINEN eigenen Messweg', () => {
  // **Der Kern von „kein zweiter Messweg".** Käme hier ein eigenes `matchMedia` oder ein
  // `ResizeObserver` hinzu, gäbe es zwei Orte, an denen „schmal" entschieden wird — und sie
  // driften auseinander, sobald einer angefasst wird.
  for (const muster of [/matchMedia/, /ResizeObserver/, /innerWidth/, /getBoundingClientRect/]) {
    assert.doesNotMatch(hinweis, muster, `die Fläche misst selbst: ${muster}`);
  }
});

// --- K-02: die Verriegelung gegen Medienabfragen bleibt -------------------------------------------

test('K-02: der Weg braucht kein `@media` — weder in der Fläche noch in ihren Regeln', () => {
  // Die Zusage aus `stilschicht.test.ts` („Responsive ist L7") bleibt grün, weil dieser Weg
  // sie nicht braucht: die Bedingung lebt in JavaScript, wo sie schon lebte.
  const block = css.slice(css.indexOf('.hp-mb-flaeche'));
  assert.ok(block.length > 200, 'die Regeln der Fläche wurden nicht gefunden — die Zusage misst Leere');
  assert.doesNotMatch(block, /@media/, 'die Fläche bringt eine Medienabfrage in die Stilschicht');
  // Und keine Rohfarbe: alles über Token.
  const roh = [...block.matchAll(/#[0-9a-fA-F]{3,8}\b|rgba?\(/g)].map((m) => m[0]);
  assert.deepEqual(roh, [], `Rohfarbe in den Regeln der Fläche: ${roh.join(', ')}`);
});

// --- K-03: ehrlich, nicht endgültig ----------------------------------------------------------------

test('K-03: der Satz nennt Mindestbreite, Grund und Weg zurück', () => {
  // **Zeilenumbrüche geglättet, bevor gesucht wird.** Im JSX steht der Satz über mehrere Zeilen;
  // ein Muster, das ihn ununterbrochen sucht, fände ihn nie — und meldete einen Mangel, den es
  // nicht gibt. *Meine erste Fassung tat genau das.*
  const text = hinweis.replace(/\s+/g, ' ');
  assert.match(text, /\{MINDESTBREITE_PX\} px/, 'der Satz nennt die Mindestbreite nicht');
  assert.match(text, /nicht erreichbar, auch nicht durch Scrollen/, 'der Satz nennt den Grund nicht');
  assert.match(text, /Fenster breiter|grösseren Bildschirm/, 'der Satz nennt keinen Weg zurück');
});

test('K-03: der Hinweis ist eine VERTAGUNG, keine Absage', () => {
  // Mutation „aus der Vertagung wird eine Absage" kam durch. L7 ist vertagt, nicht gestrichen —
  // ein Satz, der das Gegenteil sagt, altert zu einer Unwahrheit, sobald L7 gebaut wird.
  assert.match(hinweis, /geplant, aber noch nicht gebaut/,
    'der Hinweis behauptet, dass es nie gehen wird — L7 ist eine Vertagung');
  for (const wort of ['nicht vorgesehen', 'nicht möglich', 'wird nicht']) {
    assert.ok(!hinweis.includes(wort), `der Hinweis schliesst aus statt zu vertagen: „${wort}"`);
  }
});

test('K-03: die Fläche wird angesagt — sie ist kein stummes Overlay', () => {
  // Mutation „Rolle für Vorleseprogramme entfernt" kam durch. Wer den Planer mit einem
  // Vorleseprogramm öffnet, bekäme sonst eine leere Seite ohne Erklärung.
  assert.match(hinweis, /role="status"/, 'die Fläche wird Vorleseprogrammen nicht angesagt');
});

// --- K-04: kein Zustand ----------------------------------------------------------------------------

test('K-04: die Fläche hält keinen Zustand — sie bekommt gesagt, ob sie erscheint', () => {
  for (const muster of [/useState/, /usePlannerUiStore/, /localStorage/, /useEffect/]) {
    assert.doesNotMatch(hinweis, muster, `die Fläche hält Zustand: ${muster}`);
  }
  // presence-Partner: den Zustand hält weiterhin die Hauptfunktion.
  assert.match(app, /const istSchmal = useIstSchmal\(\)/, 'der Schalter ist aus der Hauptfunktion verschwunden');
});
