/**
 * platziereTreppenStufe: lokaler Stufen-Quader -> three-Welt (m), konsistent zu platziereWandQuader.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { platziereTreppenStufe } from '../renderers/three-d/platzierung';

const stufe = { mitte: [100, 0, 87.5] as [number, number, number], groesse: [200, 1000, 175] as [number, number, number] };

test('waagerechte Treppe: erste Stufe korrekt in three (m) platziert', () => {
  const pl = platziereTreppenStufe({ x: 0, y: 0 }, { x: 3000, y: 0 }, stufe, 0);
  assert.ok(Math.abs(pl.zentrum.x - 0.1) < 1e-9);       // welt x=100mm -> 0.1m
  assert.ok(Math.abs(pl.zentrum.y - 0.0875) < 1e-9);    // welt z=87,5mm -> three y
  assert.ok(Math.abs(pl.zentrum.z - 0) < 1e-9);
  assert.equal(pl.rotationY, 0);
  assert.deepEqual(pl.masse, { x: 0.2, y: 0.175, z: 1 }); // Auftritt / Hoehe / Laufbreite
});

test('senkrechte Treppe dreht um 90 Grad (rotationY = pi/2)', () => {
  const pl = platziereTreppenStufe({ x: 0, y: 0 }, { x: 0, y: 3000 }, stufe, 0);
  assert.ok(Math.abs(pl.rotationY - Math.PI / 2) < 1e-9);
});

test('Level-Elevation hebt die Stufe an', () => {
  const pl = platziereTreppenStufe({ x: 0, y: 0 }, { x: 3000, y: 0 }, stufe, 3000);
  assert.ok(Math.abs(pl.zentrum.y - (3000 + 87.5) / 1000) < 1e-9);
});
