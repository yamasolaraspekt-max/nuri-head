/**
 * P2b-3-Fix: Außenmaßkette aus Wandband-Ecken (Außenmaß + Mauerdicke, nicht Achsmaß).
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { punkteMassketten } from '../geometry/masskette';

const bandEcken = [
  { x: -120, y: -120 }, { x: 120, y: 120 }, { x: 6120, y: -120 }, { x: 5880, y: 120 },
  { x: -120, y: 4120 }, { x: 120, y: 3880 }, { x: 6120, y: 4120 }, { x: 5880, y: 3880 },
];

test('X-Kette zeigt Mauerdicke + lichtes Maß + Mauerdicke (240 | 5760 | 240)', () => {
  const r = punkteMassketten(bandEcken);
  assert.deepEqual(r.xKette.map((s) => s.laenge), [240, 5760, 240]);
});
test('Y-Kette analog (240 | 3760 | 240)', () => {
  const r = punkteMassketten(bandEcken);
  assert.deepEqual(r.yKette.map((s) => s.laenge), [240, 3760, 240]);
});
test('Außenmaß = Summe der Kette = Außenkante–Außenkante', () => {
  const r = punkteMassketten(bandEcken);
  assert.equal(r.xKette.reduce((a, s) => a + s.laenge, 0), 6240);
  assert.equal(r.yKette.reduce((a, s) => a + s.laenge, 0), 4240);
  assert.equal(r.bbox.maxX - r.bbox.minX, 6240);
});
test('Achsmaß wäre falsch: Außenmaß ist um volle Wanddicke größer', () => {
  const r = punkteMassketten(bandEcken);
  assert.equal(r.xKette.reduce((a, s) => a + s.laenge, 0) - 6000, 240);
});
test('nahe Flächen deduppen (kein 0-Segment)', () => {
  const r = punkteMassketten([{ x: 0, y: 0 }, { x: 0.4, y: 0 }, { x: 1000, y: 0 }]);
  assert.deepEqual(r.xKette.map((s) => s.laenge), [1000]);
});
test('leer → keine Kette, bbox null', () => {
  const r = punkteMassketten([]);
  assert.deepEqual(r.xKette, []);
  assert.equal(r.bbox, null);
});
