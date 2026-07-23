/**
 * W-3b Stufe 2a Teil 2 — L/T/U-Rendering via additivem RoofNode.anbau.
 * Belegt: additiv/kein 422; U rendert echte Flächen (dachUForm); l/t bewusst leer (nur Linien portiert);
 * fehlendes/degeneriertes anbau → leer, kein Crash; SSOT (dachRoh eine Quelle).
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { sceneDocumentSchema } from '../domain/validation';
import { dachMeshWelt, dachflaechen } from '../renderers/three-d/dachMesh';
import type { SceneDocument, RoofNode, RoofShape, RoofAnbauMasse } from '../domain/scene.types';

const ISO = '2026-07-23T00:00:00.000Z';
const U_ANBAU: RoofAnbauMasse = { length: 12000, width: 8000, lengthB: 4000, widthB: 3000 };

function roof(roofType: RoofShape, anbau?: RoofAnbauMasse): RoofNode {
  return {
    id: 'r1', type: 'roof', levelId: 'l1', visible: true, locked: false, tags: [],
    createdAt: ISO, updatedAt: ISO,
    polygon: [{ x: 0, y: 0 }, { x: 12000, y: 0 }, { x: 12000, y: 8000 }, { x: 0, y: 8000 }],
    roofType, neigungGrad: 35, firstAzimutGrad: 90, ueberstandMm: 400, traufhoeheMm: 6000,
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

test('additiv: RoofNode OHNE anbau validiert unverändert (kein 422)', () => {
  assert.equal(sceneDocumentSchema.safeParse(doc(roof('sattel'))).success, true);
});

test('additiv: RoofNode MIT anbau validiert (optionales Feld)', () => {
  assert.equal(sceneDocumentSchema.safeParse(doc(roof('u-shape', U_ANBAU))).success, true);
});

test('U rendert echte Flächen aus dachUForm (nicht mehr leer)', () => {
  const mesh = dachMeshWelt(roof('u-shape', U_ANBAU));
  assert.ok(mesh.dreiecke.length > 0, 'u-shape mit anbau liefert Dreiecke');
  assert.ok(mesh.firstHoeheMm > 6000, 'Firsthöhe über der Traufe');
  // U-Flächen sind nicht rechteckig ⇒ keine Aufbau-Trägerflächen
  assert.deepEqual(dachflaechen(roof('u-shape', U_ANBAU)), []);
});

test('Fix: u-shape mit NUR length/width (kein lengthB/widthB) → U rendert nicht-leer (war maskiert)', () => {
  const mesh = dachMeshWelt(roof('u-shape', { length: 12000, width: 8000 }));
  assert.ok(mesh.dreiecke.length > 0, 'u-shape mit nur Hauptbau-Maßen muss rendern (Schenkel abgeleitet)');
  assert.ok(mesh.firstHoeheMm > 6000, 'Firsthöhe über der Traufe');
});

test('U ohne anbau → leer + kein Crash (Marker-Pfad)', () => {
  const mesh = dachMeshWelt(roof('u-shape'));
  assert.deepEqual(mesh.dreiecke, []);
});

test('U mit degeneriertem anbau (Anbau breiter als Hauptbau) → leer, kein Crash', () => {
  const mesh = dachMeshWelt(roof('u-shape', { length: 12000, width: 8000, lengthB: 4000, widthB: 9000 }));
  assert.deepEqual(mesh.dreiecke, []);
});

test('l/t bleiben (noch) leer — nur Kehl-/Gratlinien portiert, Flächen Stufe C', () => {
  assert.deepEqual(dachMeshWelt(roof('l-shape', U_ANBAU)).dreiecke, []);
  assert.deepEqual(dachMeshWelt(roof('t-shape', U_ANBAU)).dreiecke, []);
});
