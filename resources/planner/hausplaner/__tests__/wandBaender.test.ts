/**
 * Wandbänder mit Gehrung (P2b-2). Reine Geometrie — deterministische mm-Punkte.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { wandBaender, type WandEingabe } from '../geometry/wallGeometry';

function band(id: string, waende: WandEingabe[]) {
  return wandBaender(waende).find((b) => b.id === id);
}

test('90°-L-Ecke: beide Wände treffen sich auf Gehrung an derselben Diagonale', () => {
  // Wand A läuft nach Osten und endet bei (4000,0); Wand B läuft von dort nach Norden.
  const A: WandEingabe = { id: 'a', start: { x: 0, y: 0 }, end: { x: 4000, y: 0 }, thickness: 240 };
  const B: WandEingabe = { id: 'b', start: { x: 4000, y: 0 }, end: { x: 4000, y: 3000 }, thickness: 240 };
  const bA = band('a', [A, B])!;
  const bB = band('b', [A, B])!;

  assert.deepEqual(bA.ecken[0], { x: 0, y: 120 });
  assert.deepEqual(bA.ecken[3], { x: 0, y: -120 });
  const aEnde = [bA.ecken[1], bA.ecken[2]];
  assert.ok(aEnde.some((p) => p.x === 4120 && p.y === 120));
  assert.ok(aEnde.some((p) => p.x === 3880 && p.y === -120));
  const bStart = [bB.ecken[0], bB.ecken[3]];
  assert.ok(bStart.some((p) => p.x === 3880 && p.y === -120));
  assert.ok(bStart.some((p) => p.x === 4120 && p.y === 120));
});

test('Freies Ende bleibt stumpf (keine Gehrung ohne Nachbar)', () => {
  const A: WandEingabe = { id: 'a', start: { x: 0, y: 0 }, end: { x: 4000, y: 0 }, thickness: 240 };
  const b = band('a', [A])!;
  assert.deepEqual(b.ecken, [
    { x: 0, y: 120 },
    { x: 4000, y: 120 },
    { x: 4000, y: -120 },
    { x: 0, y: -120 },
  ]);
});

test('Gerade Fortsetzung (180°) fällt auf stumpfen Abschluss zurück — kein Miter-Sprung', () => {
  const A: WandEingabe = { id: 'a', start: { x: 0, y: 0 }, end: { x: 4000, y: 0 }, thickness: 240 };
  const B: WandEingabe = { id: 'b', start: { x: 4000, y: 0 }, end: { x: 8000, y: 0 }, thickness: 240 };
  const bA = band('a', [A, B])!;
  assert.deepEqual(bA.ecken[1], { x: 4000, y: 120 });
  assert.deepEqual(bA.ecken[2], { x: 4000, y: -120 });
});

test('T-Stoß (drei Wände an einem Punkt) bleibt stumpf', () => {
  const A: WandEingabe = { id: 'a', start: { x: 0, y: 0 }, end: { x: 4000, y: 0 }, thickness: 240 };
  const B: WandEingabe = { id: 'b', start: { x: 4000, y: 0 }, end: { x: 8000, y: 0 }, thickness: 240 };
  const C: WandEingabe = { id: 'c', start: { x: 4000, y: 0 }, end: { x: 4000, y: 3000 }, thickness: 240 };
  const bC = band('c', [A, B, C])!;
  assert.deepEqual(bC.ecken[0], { x: 3880, y: 0 });
  assert.deepEqual(bC.ecken[3], { x: 4120, y: 0 });
});

test('Alle Bandecken sind ganzzahlig (mm-Invariante)', () => {
  const A: WandEingabe = { id: 'a', start: { x: 0, y: 0 }, end: { x: 3000, y: 0 }, thickness: 300 };
  const B: WandEingabe = { id: 'b', start: { x: 3000, y: 0 }, end: { x: 5121, y: 2121 }, thickness: 300 };
  for (const b of wandBaender([A, B])) {
    for (const p of b.ecken) {
      assert.ok(Number.isInteger(p.x) && Number.isInteger(p.y), `nicht ganzzahlig: ${JSON.stringify(p)}`);
    }
  }
});

test('Länge-0-Wand wird übersprungen (kein Band, kein Absturz)', () => {
  const A: WandEingabe = { id: 'a', start: { x: 100, y: 100 }, end: { x: 100, y: 100 }, thickness: 240 };
  const B: WandEingabe = { id: 'b', start: { x: 0, y: 0 }, end: { x: 2000, y: 0 }, thickness: 240 };
  const baender = wandBaender([A, B]);
  assert.equal(baender.length, 1);
  assert.equal(baender[0].id, 'b');
});
