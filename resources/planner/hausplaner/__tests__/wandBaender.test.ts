/**
 * Wandbänder mit Gehrung (P2b-2). Reine Geometrie — deterministische mm-Punkte.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { wandBaender, type WandEingabe } from '../geometry/wallGeometry';

function band(id: string, waende: WandEingabe[]) {
  return wandBaender(waende).find((b) => b.id === id);
}

test('90°-L-Ecke: Naht läuft vom Inneneck zum SCHARFEN Außeneck (90° außen bleibt spitz)', () => {
  // Wand A läuft nach Osten und endet bei (4000,0); Wand B läuft von dort nach Norden.
  const A: WandEingabe = { id: 'a', start: { x: 0, y: 0 }, end: { x: 4000, y: 0 }, thickness: 240 };
  const B: WandEingabe = { id: 'b', start: { x: 4000, y: 0 }, end: { x: 4000, y: 3000 }, thickness: 240 };
  const bA = band('a', [A, B])!;
  const bB = band('b', [A, B])!;

  const hat = (b: typeof bA, x: number, y: number) => b.ecken.some((p) => p.x === x && p.y === y);

  // Scharfer Außeneck = Schnitt der beiden AUSSENkanten (Wand-A-Südkante y=−120 × Wand-B-Ostkante x=4120).
  const AUSSEN = { x: 4120, y: -120 };
  // Inneneck = Schnitt der beiden Innenkanten (y=+120 × x=3880).
  const INNEN = { x: 3880, y: 120 };

  // Beide Wände tragen BEIDE Naht-Eckpunkte ⇒ geteilte 45°-Naht, scharfer 90°-Außeneck, kein Loch.
  assert.ok(hat(bA, AUSSEN.x, AUSSEN.y) && hat(bA, INNEN.x, INNEN.y), 'Wand A: Innen- und Außeneck');
  assert.ok(hat(bB, AUSSEN.x, AUSSEN.y) && hat(bB, INNEN.x, INNEN.y), 'Wand B: Innen- und Außeneck');

  // Falsche (quer geschnittene) Diagonale darf NICHT mehr auftreten (Regression P2b-2-Fix).
  assert.ok(!hat(bA, 4120, 120) && !hat(bA, 3880, -120), 'Wand A schneidet nicht quer');

  // Freies Start-Ende von A bleibt stumpf.
  assert.deepEqual(bA.ecken[0], { x: 0, y: 120 });
  assert.deepEqual(bA.ecken[3], { x: 0, y: -120 });
});

test('Freies Ende bleibt stumpf (keine Gehrung ohne Nachbar)', () => {
  const A: WandEingabe = { id: 'a', start: { x: 0, y: 0 }, end: { x: 4000, y: 0 }, thickness: 240 };
  const b = band('a', [A])!;
  // Alle vier Ecken exakt ±120 in y an den Enden — reines Rechteck.
  assert.deepEqual(b.ecken, [
    { x: 0, y: 120 },
    { x: 4000, y: 120 },
    { x: 4000, y: -120 },
    { x: 0, y: -120 },
  ]);
});

test('Gerade Fortsetzung (180°) fällt auf stumpfen Abschluss zurück — kein Miter-Sprung', () => {
  // Zwei kollineare Wände, die sich bei (4000,0) berühren: p und q sind entgegengesetzt → keine Gehrung.
  const A: WandEingabe = { id: 'a', start: { x: 0, y: 0 }, end: { x: 4000, y: 0 }, thickness: 240 };
  const B: WandEingabe = { id: 'b', start: { x: 4000, y: 0 }, end: { x: 8000, y: 0 }, thickness: 240 };
  const bA = band('a', [A, B])!;
  // A endet stumpf bei x=4000 (Gehrung entartet → Rechteck-Kante).
  assert.deepEqual(bA.ecken[1], { x: 4000, y: 120 });
  assert.deepEqual(bA.ecken[2], { x: 4000, y: -120 });
});

test('T-Stoß (drei Wände an einem Punkt) bleibt stumpf', () => {
  const A: WandEingabe = { id: 'a', start: { x: 0, y: 0 }, end: { x: 4000, y: 0 }, thickness: 240 };
  const B: WandEingabe = { id: 'b', start: { x: 4000, y: 0 }, end: { x: 8000, y: 0 }, thickness: 240 };
  const C: WandEingabe = { id: 'c', start: { x: 4000, y: 0 }, end: { x: 4000, y: 3000 }, thickness: 240 };
  const bC = band('c', [A, B, C])!;
  // C dockt an einem 3-Wand-Punkt an → Start stumpf (±120 in x um die Achse x=4000).
  assert.deepEqual(bC.ecken[0], { x: 3880, y: 0 });
  assert.deepEqual(bC.ecken[3], { x: 4120, y: 0 });
});

test('Alle Bandecken sind ganzzahlig (mm-Invariante)', () => {
  // 45°-Ecke erzeugt irrationale Zwischenwerte — Ergebnis muss dennoch ganzzahlig sein.
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
