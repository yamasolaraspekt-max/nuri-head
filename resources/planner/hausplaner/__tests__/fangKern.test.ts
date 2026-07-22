/**
 * L3b Fang-Kern: Priorität Endpunkt > Ortho > Raster, Toleranz, Toggle.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { fange, wandFangpunkte } from '../geometry/fangKern';

test('Fang aus → Rohpunkt (gerundet), art keiner', () => {
  const r = fange({ x: 1234.6, y: 5678.4 }, [{ x: 1235, y: 5678 }], { toleranzMm: 50, aktiv: false });
  assert.deepEqual(r.punkt, { x: 1235, y: 5678 });
  assert.equal(r.art, 'keiner');
});

test('Endpunkt in Toleranz → snappt auf den Kandidaten', () => {
  const r = fange({ x: 1020, y: 30 }, [{ x: 1000, y: 0 }, { x: 5000, y: 0 }], { toleranzMm: 50 });
  assert.deepEqual(r.punkt, { x: 1000, y: 0 });
  assert.equal(r.art, 'endpunkt');
});

test('Endpunkt außerhalb Toleranz → kein Endpunkt-Fang', () => {
  const r = fange({ x: 1200, y: 0 }, [{ x: 1000, y: 0 }], { toleranzMm: 50, raster: 100 });
  assert.notEqual(r.art, 'endpunkt');
});

test('nächster Endpunkt gewinnt bei mehreren in Toleranz', () => {
  const r = fange({ x: 1010, y: 0 }, [{ x: 1000, y: 0 }, { x: 1030, y: 0 }], { toleranzMm: 60 });
  assert.deepEqual(r.punkt, { x: 1000, y: 0 });
});

test('Ortho waagerecht: nahe der x-Achse durch Referenz → y auf Referenz', () => {
  const r = fange({ x: 5000, y: 40 }, [], { toleranzMm: 50, ortho: { x: 0, y: 0 }, orthoToleranzMm: 100 });
  assert.deepEqual(r.punkt, { x: 5000, y: 0 });
  assert.equal(r.art, 'ortho');
});

test('Ortho senkrecht: nahe der y-Achse → x auf Referenz', () => {
  const r = fange({ x: 40, y: 5000 }, [], { toleranzMm: 50, ortho: { x: 0, y: 0 }, orthoToleranzMm: 100 });
  assert.deepEqual(r.punkt, { x: 0, y: 5000 });
  assert.equal(r.art, 'ortho');
});

test('Priorität: Endpunkt schlägt Ortho und Raster', () => {
  const r = fange({ x: 12, y: 12 }, [{ x: 0, y: 0 }], { toleranzMm: 50, ortho: { x: 0, y: 0 }, raster: 100 });
  assert.equal(r.art, 'endpunkt');
  assert.deepEqual(r.punkt, { x: 0, y: 0 });
});

test('Raster als Fallback rundet beide Achsen', () => {
  const r = fange({ x: 1234, y: 5678 }, [], { toleranzMm: 20, raster: 100 });
  assert.deepEqual(r.punkt, { x: 1200, y: 5700 });
  assert.equal(r.art, 'raster');
});

test('kein Kandidat, kein Ortho, kein Raster → keiner', () => {
  const r = fange({ x: 333, y: 444 }, [], { toleranzMm: 20 });
  assert.equal(r.art, 'keiner');
  assert.deepEqual(r.punkt, { x: 333, y: 444 });
});

test('Determinismus', () => {
  const args = [{ x: 1010, y: 20 }, [{ x: 1000, y: 0 }], { toleranzMm: 50, raster: 100, ortho: { x: 0, y: 0 } }] as const;
  assert.deepEqual(fange(...args), fange(...args));
});

test('wandFangpunkte: Endpunkte + Mittelpunkt je Wand', () => {
  const pts = wandFangpunkte([{ start: { x: 0, y: 0 }, end: { x: 1000, y: 0 } }]);
  assert.equal(pts.length, 3);
  assert.deepEqual(pts[0], { x: 0, y: 0 });
  assert.deepEqual(pts[1], { x: 1000, y: 0 });
  assert.deepEqual(pts[2], { x: 500, y: 0 });
});

test('wandFangpunkte: leer -> leer', () => {
  assert.deepEqual(wandFangpunkte([]), []);
});
