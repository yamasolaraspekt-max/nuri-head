import { test } from 'node:test';
import assert from 'node:assert/strict';
import { wandLaenge, punktAufWand, azimutDerNormalen, istGanzzahlig } from '../geometry/wallGeometry';

test('wandLaenge: 3-4-5-Dreieck', () => {
  assert.equal(wandLaenge({ x: 0, y: 0 }, { x: 3000, y: 4000 }), 5000);
});

test('punktAufWand: Mitte einer 4 m-Wand', () => {
  assert.deepEqual(punktAufWand({ x: 0, y: 0 }, { x: 4000, y: 0 }, 2000), { x: 2000, y: 0 });
});

test('punktAufWand rundet ganzzahlig (mm-Invariante)', () => {
  const p = punktAufWand({ x: 0, y: 0 }, { x: 3000, y: 4000 }, 1234);
  assert.ok(istGanzzahlig(p), 'Punkt muss ganzzahlig sein');
});

test('azimut: Wand West→Ost, linke Normale zeigt Nord (0°)', () => {
  assert.equal(azimutDerNormalen({ x: 0, y: 0 }, { x: 4000, y: 0 }, 'links'), 0);
});

test('azimut: Wand West→Ost, rechte Normale zeigt Süd (180°)', () => {
  assert.equal(azimutDerNormalen({ x: 0, y: 0 }, { x: 4000, y: 0 }, 'rechts'), 180);
});

test('azimut: Wand Süd→Nord, linke Normale zeigt West (270°)', () => {
  assert.equal(azimutDerNormalen({ x: 0, y: 0 }, { x: 0, y: 4000 }, 'links'), 270);
});

test('azimut folgt der Drehung der Wand (Gegen-Beweis Abnahmekriterium 9)', () => {
  const nord = azimutDerNormalen({ x: 0, y: 0 }, { x: 4000, y: 0 }, 'links');
  const ost = azimutDerNormalen({ x: 0, y: 0 }, { x: 0, y: -4000 }, 'links'); // Wand Nord→Süd ⇒ linke Normale Ost
  assert.equal(nord, 0);
  assert.equal(ost, 90);
});
