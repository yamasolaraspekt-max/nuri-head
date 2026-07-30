/**
 * AUF-51 — der Verschub der Zeichenfläche.
 *
 * **Der Fehler war ein Widerspruch, kein Layout-Mangel:** die Bühne war `draggable`, hatte keinen
 * Drag-Handler, und ihre Position stand als gesteuerter Wert **ohne Zustand**. Jedes Rendern —
 * und `onMouseMove` rendert bei jeder Mausbewegung — setzte sie zurück. `weltPunkt` las derweil
 * `stage.x()`, also die **echte** Lage: Anzeige und Koordinate widersprachen sich.
 *
 * Geprüft wird deshalb beides: die Regel (rein, ohne DOM) **und** die Verdrahtung, ohne die die
 * Regel wirkungslos bliebe.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';
import { panAus, standardPan, istVerschoben, STANDARD_PAN_X, STANDARD_PAN_RAND } from '../app/dashboard/pan';

const hier = dirname(fileURLToPath(import.meta.url));
const ohneKommentare = (s: string): string => s.replace(/\/\*[\s\S]*?\*\//g, '').replace(/^\s*\/\/.*$/gm, '');
const app = ohneKommentare((readFileSync(join(hier, '../app/HausplanerApp.tsx'), 'utf8')
  // AUF-48 Scheibe 4a: der Kopfrahmen (Werkzeugzeile, Arbeitsbereich-Waehler,
  // Bedien-Werkzeugleiste) ist nach `dashboard/Kopfrahmen.tsx` ausgezogen. **Beide Dateien
  // werden gelesen** — die geprueften Eigenschaften sind unveraendert, und eine Absenz-Zusage
  // darf nicht dadurch gruen werden, dass Inhalt eine Datei weiter gewandert ist.
  + readFileSync(join(hier, '../app/dashboard/Kopfrahmen.tsx'), 'utf8')));

// --- Die Regel ----------------------------------------------------------------------------------
test('nie verschoben ⇒ Standardlage, und die folgt der Fensterhöhe', () => {
  assert.deepEqual(panAus(null, 900), { x: 80, y: 820 });
  assert.deepEqual(panAus(null, 600), { x: 80, y: 520 }, 'kleineres Fenster ⇒ die Lage rückt mit');
  assert.deepEqual(standardPan(900), panAus(null, 900));
  assert.equal(STANDARD_PAN_X, 80, 'unverändert der Wert, der vorher fest im JSX stand');
  assert.equal(STANDARD_PAN_RAND, 80);
});

test('selbst verschoben ⇒ der eigene Wert gilt, unabhängig von der Fensterhöhe', () => {
  const meins = { x: -1200, y: 3400 };
  assert.deepEqual(panAus(meins, 900), meins);
  assert.deepEqual(panAus(meins, 300), meins, 'ein Fenstergrößen-Wechsel darf den Verschub nicht überschreiben');
});

test('`null` heißt „nie verschoben" — das ist der Unterschied, den der Startwert trägt', () => {
  assert.equal(istVerschoben(null), false);
  assert.equal(istVerschoben({ x: 80, y: 820 }), true, 'auch der Standardwert zählt als verschoben, wenn er gesetzt wurde');
});

// --- Die Verdrahtung, ohne die die Regel wirkungslos bliebe -------------------------------------
test('die Bühne hat einen Zustand hinter ihrer Position — kein fester Wert mehr im JSX', () => {
  assert.doesNotMatch(app, /^\s*x=\{80\}$/m, 'die feste 80 war die halbe Ursache');
  assert.doesNotMatch(app, /^\s*y=\{hoehe - 80\}$/m);
  assert.match(app, /\{\.\.\.panAus\(pan, hoehe\)\}/);
  assert.match(app, /const \[pan, setPan\] = useState<Pan \| null>\(null\);/);
});

test('die Bühne hat jetzt Drag-Handler — vorher hatte sie KEINEN', () => {
  // Genau das meldete Playwright: `draggable` ohne Handler an einem gesteuerten Knoten.
  assert.match(app, /onDragMove=\{\(e\) => \{ if \(e\.target === e\.currentTarget\) setPan/);
  assert.match(app, /onDragEnd=\{\(e\) => \{ if \(e\.target === e\.currentTarget\) setPan/);
});

test('nur die BÜHNE schreibt den Verschub — ein gezogenes Bauteil nicht', () => {
  // Ohne die Herkunftsprüfung würde jedes gezogene Objekt den Verschub überschreiben: Node-Drags
  // steigen bis zur Bühne auf, und `e.target` wäre dann die Wand, nicht die Bühne.
  const treffer = app.match(/setPan\(\{ x: e\.target\.x\(\), y: e\.target\.y\(\) \}\)/g) ?? [];
  assert.equal(treffer.length, 2, 'genau zwei Schreibstellen: DragMove und DragEnd');
  const geschuetzt = app.match(/if \(e\.target === e\.currentTarget\) setPan/g) ?? [];
  assert.equal(geschuetzt.length, 2, 'beide sind gegen fremde Ziele geschützt');
});

test('`weltPunkt` liest weiterhin die ECHTE Bühnenlage — daran war nichts falsch', () => {
  // Der Fehler lag nie in der Koordinatenrechnung; sie war die Seite, die recht hatte.
  assert.match(app, /\(zeiger\.x - stage\.x\(\)\) \/ zoom/);
  assert.match(app, /-\(\(zeiger\.y - stage\.y\(\)\) \/ zoom\)/);
});

test('verschoben wird nur mit dem Auswahl-Werkzeug — unverändert', () => {
  assert.match(app, /draggable=\{werkzeug === 'auswahl'\}/);
});
