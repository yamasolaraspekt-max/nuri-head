/**
 * Verdrahtung #1 — Daten-Round-Trip, den das Dach-Panel (L/T/U-select + Anbaufelder) produziert:
 * roofType ∈ {l/t/u-shape} + anbau → SceneDocument valide (speicherbar/reload-fest) UND 3D rendert.
 * Operanden-Gate: unvollständige Maße bleiben speicherbar, rendern aber NICHT (Panel markiert den Grund).
 * (Die JSX-Verdrahtung selbst ist Browser-Sache; hier ist der Modell-/Render-/Save-Vertrag verriegelt.)
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { sceneDocumentSchema } from '../domain/validation';
import { dachMeshWelt } from '../renderers/three-d/dachMesh';
import { istVerschneidungsForm } from '../domain/roofShape';
import type { SceneDocument, RoofNode, RoofAnbauMasse, RoofShape } from '../domain/scene.types';

const ISO = '2026-01-01T00:00:00.000Z';
function roof(roofType: RoofShape, anbau?: RoofAnbauMasse): RoofNode {
  return {
    id: 'r1', type: 'roof', levelId: 'l1', visible: true, locked: false, tags: [], createdAt: ISO, updatedAt: ISO,
    polygon: [{ x: 0, y: 0 }, { x: 12000, y: 0 }, { x: 12000, y: 8000 }, { x: 0, y: 8000 }],
    roofType, neigungGrad: 35, firstAzimutGrad: 0, ueberstandMm: 500, traufhoeheMm: 6000,
    ...(anbau ? { anbau } : {}),
  };
}
function doc(r: RoofNode): SceneDocument {
  return {
    id: 'd', projectId: 1, schemaVersion: 2, revision: 1, units: 'mm',
    settings: { gridSize: 100, snapEnabled: true, angleSnap: 15 },
    levels: [{ id: 'l1', name: 'DG', elevation: 0, defaultWallHeight: 2500, floorThickness: 200, sortOrder: 0 }],
    nodes: [], materials: [], roofs: [r], metadata: { createdAt: ISO, updatedAt: ISO },
  };
}

test('l/t/u sind Verschneidungsformen (die neuen select-Optionen)', () => {
  for (const f of ['l-shape', 't-shape', 'u-shape'] as const) assert.ok(istVerschneidungsForm(f), f);
  for (const f of ['sattel', 'walm', 'pult', 'flach'] as const) assert.ok(!istVerschneidungsForm(f), f);
});

test('u-shape + alle vier Anbaumaße → speicherbar (Schema) UND rendert', () => {
  const r = roof('u-shape', { length: 12000, width: 8000, lengthB: 4000, widthB: 4000 });
  assert.equal(sceneDocumentSchema.safeParse(doc(r)).success, true, 'reload-/speicherfest');
  assert.ok(dachMeshWelt(r).dreiecke.length > 0, 'rendert');
});

test('l-shape + Außenmaße → speicherbar UND rendert', () => {
  const r = roof('l-shape', { length: 12000, width: 8000, lengthB: 4000, widthB: 4000 });
  assert.equal(sceneDocumentSchema.safeParse(doc(r)).success, true);
  assert.ok(dachMeshWelt(r).dreiecke.length > 0);
});

test('Operanden-Gate: u-shape OHNE Innenhof-Maße → speicherbar, aber rendert NICHT (Marker-Fall)', () => {
  const r = roof('u-shape', { length: 12000, width: 8000 }); // lengthB/widthB fehlen
  assert.equal(sceneDocumentSchema.safeParse(doc(r)).success, true, 'Teil-anbau bleibt schema-valide');
  assert.deepEqual(dachMeshWelt(r).dreiecke, [], 'ohne alle vier Maße kein Render (Panel markiert den Grund)');
});
