/**
 * Editier-Geometrie (Bewegen / Duplizieren / Spiegeln). Reine mm-Geometrie.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import {
  versetzePunkt,
  versetzteWand,
  spiegelePunkt,
  spiegelteWand,
  bbox,
  achsenMitte,
} from '../geometry/editierGeometrie';

test('versetzePunkt: verschiebt und rundet ganzzahlig', () => {
  assert.deepEqual(versetzePunkt({ x: 100, y: 200 }, 50.4, -25.6), { x: 150, y: 174 });
});

test('versetzteWand: beide Endpunkte um denselben Versatz', () => {
  const w = versetzteWand({ x: 0, y: 0 }, { x: 4000, y: 0 }, 500, 300);
  assert.deepEqual(w, { start: { x: 500, y: 300 }, end: { x: 4500, y: 300 } });
});

test('spiegelePunkt vertikal: x an Achse gespiegelt, y bleibt', () => {
  assert.deepEqual(spiegelePunkt({ x: 200, y: 500 }, 'vertikal', 1000), { x: 1800, y: 500 });
});

test('spiegelePunkt horizontal: y an Achse gespiegelt, x bleibt', () => {
  assert.deepEqual(spiegelePunkt({ x: 200, y: 500 }, 'horizontal', 1000), { x: 200, y: 1500 });
});

test('Doppelte Spiegelung an derselben Achse ergibt den Ausgangspunkt', () => {
  const p = { x: 337, y: 912 };
  const einmal = spiegelePunkt(p, 'vertikal', 1234);
  assert.deepEqual(spiegelePunkt(einmal, 'vertikal', 1234), p);
});

test('spiegelteWand vertikal: Wand an Achse gespiegelt', () => {
  const w = spiegelteWand({ x: 0, y: 0 }, { x: 2000, y: 0 }, 'vertikal', 1000);
  assert.deepEqual(w, { start: { x: 2000, y: 0 }, end: { x: 0, y: 0 } });
});

test('bbox + achsenMitte: Mittelachse durch die Auswahl', () => {
  const b = bbox([{ x: 0, y: 0 }, { x: 6000, y: 0 }, { x: 6000, y: 4000 }])!;
  assert.deepEqual(b, { minX: 0, maxX: 6000, minY: 0, maxY: 4000 });
  assert.equal(achsenMitte(b, 'vertikal'), 3000);
  assert.equal(achsenMitte(b, 'horizontal'), 2000);
});

test('bbox: leere Menge ⇒ null', () => {
  assert.equal(bbox([]), null);
});
