/**
 * Maßketten (P2b-3). Reine, deterministische mm-Geometrie.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { masskette, grundrissMassketten } from '../geometry/masskette';

test('masskette: sortiert, dedupliziert und bildet aufeinanderfolgende Abstände', () => {
  const k = masskette([6000, 0, 4000, 4000]);
  assert.deepEqual(k, [
    { von: 0, bis: 4000, laenge: 4000 },
    { von: 4000, bis: 6000, laenge: 2000 },
  ]);
});

test('masskette: Positionen innerhalb der Toleranz zählen als EIN Bezugspunkt (kein 0-Segment)', () => {
  const k = masskette([0, 1, 4000], 1); // 0 und 1 verschmelzen.
  assert.deepEqual(k, [{ von: 0, bis: 4000, laenge: 4000 }]);
});

test('masskette: rundet auf ganze mm', () => {
  const k = masskette([0.4, 2999.6]);
  assert.deepEqual(k, [{ von: 0, bis: 3000, laenge: 3000 }]);
});

test('masskette: leer / ein Punkt ⇒ keine Segmente', () => {
  assert.deepEqual(masskette([]), []);
  assert.deepEqual(masskette([1234]), []);
});

test('grundrissMassketten: Rechteck-Zimmer ⇒ je eine Gesamtstrecke + bbox', () => {
  const waende = [
    { start: { x: 500, y: 500 }, end: { x: 5500, y: 500 } },
    { start: { x: 5500, y: 500 }, end: { x: 5500, y: 3700 } },
    { start: { x: 5500, y: 3700 }, end: { x: 500, y: 3700 } },
    { start: { x: 500, y: 3700 }, end: { x: 500, y: 500 } },
  ];
  const m = grundrissMassketten(waende);
  assert.deepEqual(m.xKette, [{ von: 500, bis: 5500, laenge: 5000 }]);
  assert.deepEqual(m.yKette, [{ von: 500, bis: 3700, laenge: 3200 }]);
  assert.deepEqual(m.bbox, { minX: 500, maxX: 5500, minY: 500, maxY: 3700 });
});

test('grundrissMassketten: Innenwand teilt die x-Kette in zwei Maße', () => {
  const waende = [
    { start: { x: 0, y: 0 }, end: { x: 6000, y: 0 } },     // Südwand
    { start: { x: 3000, y: 0 }, end: { x: 3000, y: 4000 } }, // Innenwand bei x=3000
  ];
  const m = grundrissMassketten(waende);
  assert.deepEqual(m.xKette, [
    { von: 0, bis: 3000, laenge: 3000 },
    { von: 3000, bis: 6000, laenge: 3000 },
  ]);
  assert.deepEqual(m.yKette, [{ von: 0, bis: 4000, laenge: 4000 }]);
});

test('grundrissMassketten: ohne Wände ⇒ leere Ketten, bbox null', () => {
  const m = grundrissMassketten([]);
  assert.deepEqual(m.xKette, []);
  assert.deepEqual(m.yKette, []);
  assert.equal(m.bbox, null);
});
