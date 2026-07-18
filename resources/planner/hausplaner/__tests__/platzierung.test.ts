/**
 * P1b — Platzierungs-Tests (reine Mathematik, ohne three.js/WebGL):
 * Quader-Zentren, Rotations-Herleitung (θ = atan2(dy, dx)), Level-Elevation,
 * Boden-Punkte in der three-XZ-Ebene. Sollwerte handgerechnet.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { platziereWandQuader, bodenPunkteThree } from '../renderers/three-d/platzierung';
import { segmentiereWand } from '../renderers/three-d/segmentierung';
import type { WallNode } from '../domain/scene.types';

const JETZT = '2026-07-16T00:00:00.000Z';

function wand(start: { x: number; y: number }, end: { x: number; y: number }): WallNode {
  return {
    id: 'w1', type: 'wall', levelId: 'eg', visible: true, locked: false, tags: [],
    createdAt: JETZT, updatedAt: JETZT,
    start, end, thickness: 240, height: 2500,
  };
}

test('W→O-Wand: Voll-Quader [0,1500]×[0,2500] landet bei three (0.75, 1.25, 0), Rotation 0', () => {
  const p = platziereWandQuader(
    wand({ x: 0, y: 0 }, { x: 4000, y: 0 }),
    { art: 'voll', vonMm: 0, bisMm: 1500, untenMm: 0, obenMm: 2500 },
    0,
  );
  // Weltzentrum (750, 0, 1250) mm ⇒ three (0.75, 1.25, −0); Maße (1.5, 2.5, 0.24) m.
  assert.deepEqual(p.zentrum, { x: 0.75, y: 1.25, z: -0 });
  assert.equal(p.rotationY, 0);
  assert.deepEqual(p.masse, { x: 1.5, y: 2.5, z: 0.24 });
});

test('S→N-Wand: Rotation +π/2, Zentrum bei three z = −2 (Nord = −z)', () => {
  const p = platziereWandQuader(
    wand({ x: 0, y: 0 }, { x: 0, y: 4000 }),
    { art: 'voll', vonMm: 0, bisMm: 4000, untenMm: 0, obenMm: 2500 },
    0,
  );
  assert.equal(p.rotationY, Math.PI / 2);
  assert.deepEqual(p.zentrum, { x: 0, y: 1.25, z: -2 }); // Weltmitte (0, 2000) ⇒ −y/1000
});

test('3-4-5-Diagonalwand: Rotation = atan2(4000, 3000), Zentrum auf halber Achse', () => {
  const p = platziereWandQuader(
    wand({ x: 0, y: 0 }, { x: 3000, y: 4000 }),
    { art: 'voll', vonMm: 0, bisMm: 5000, untenMm: 0, obenMm: 2500 },
    0,
  );
  assert.equal(p.rotationY, Math.atan2(4000, 3000));
  // Halbe Achse = Weltpunkt (1500, 2000): three (1.5, 1.25, −2).
  assert.deepEqual(p.zentrum, { x: 1.5, y: 1.25, z: -2 });
  assert.equal(p.masse.x, 5); // volle 3-4-5-Länge in Metern
});

test('Level-Elevation wandert in three.y: OG (2800 mm) hebt das Quader-Zentrum', () => {
  const p = platziereWandQuader(
    wand({ x: 0, y: 0 }, { x: 4000, y: 0 }),
    { art: 'voll', vonMm: 0, bisMm: 4000, untenMm: 0, obenMm: 2500 },
    2800,
  );
  assert.equal(p.zentrum.y, (2800 + 1250) / 1000); // 4.05 m
});

test('Segmentierung + Platzierung zusammen: Sturz-Quader sitzt in Sturz-Höhe', () => {
  const w = wand({ x: 0, y: 0 }, { x: 4000, y: 0 });
  const s = segmentiereWand(w, [{
    id: 'f1', type: 'window', levelId: 'eg', visible: true, locked: false, tags: [],
    createdAt: JETZT, updatedAt: JETZT,
    hostWallId: 'w1', offsetFromWallStart: 1500, width: 1200, height: 1400, sillHeight: 900,
  }]);
  const sturz = s.quader.find((q) => q.art === 'sturz');
  assert.ok(sturz);
  const p = platziereWandQuader(w, sturz, 0);
  assert.equal(p.zentrum.y, (2300 + 2500) / 2 / 1000); // Mitte des Sturzes: 2.4 m
  assert.equal(p.zentrum.x, (1500 + 2700) / 2 / 1000); // Fenstermitte auf der Achse: 2.1 m
  assert.equal(p.geklemmt, false);
});

test('Klemm-Markierung wandert bis in die Platzierung (Kante 2 bleibt sichtbar)', () => {
  const w = wand({ x: 0, y: 0 }, { x: 4000, y: 0 });
  const s = segmentiereWand(w, [{
    id: 'f1', type: 'window', levelId: 'eg', visible: true, locked: false, tags: [],
    createdAt: JETZT, updatedAt: JETZT,
    hostWallId: 'w1', offsetFromWallStart: 1500, width: 1200, height: 2000, sillHeight: 900,
  }]);
  const markiert = s.quader.filter((q) => q.geklemmt).map((q) => platziereWandQuader(w, q, 0));
  assert.ok(markiert.length > 0);
  assert.ok(markiert.every((p) => p.geklemmt === true));
});

test('bodenPunkteThree: Rechteck 4×5 m liegt in der XZ-Ebene auf Level-Höhe', () => {
  const boden = bodenPunkteThree(
    [{ x: 0, y: 0 }, { x: 4000, y: 0 }, { x: 4000, y: 5000 }, { x: 0, y: 5000 }],
    2800,
  );
  assert.equal(boden.y, 2.8);
  assert.deepEqual(boden.punkte, [
    { x: 0, z: -0 }, { x: 4, z: -0 }, { x: 4, z: -5 }, { x: 0, z: -5 }, // Nord (+y) ⇒ −z
  ]);
});
