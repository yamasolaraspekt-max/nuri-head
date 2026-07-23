/**
 * W-3b Stufe 1 — RoofShape-Konsolidierung + additive roofType-Enum.
 * Belegt: (1) alle 8 Formen validieren gegen das Schema (die 4 Alt-Formen unverändert ⇒ kein 422,
 * l/t/u/rect neu gültig); (2) unbekannte Form wird abgelehnt; (3) L/T/U rendern render-neutral leer
 * (kein pauschaler Kontur-Wurf, kein Crash), rechteckige Formen unverändert; (4) eine RoofShape-Wahrheit.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { sceneDocumentSchema } from '../domain/validation';
import { ROOF_SHAPES, VERSCHNEIDUNGS_FORMEN, istVerschneidungsForm, type RoofShape } from '../domain/roofShape';
import { dachMeshWelt, dachflaechen } from '../renderers/three-d/dachMesh';
import type { SceneDocument, RoofNode } from '../domain/scene.types';

const ISO = '2026-07-23T00:00:00.000Z';

function docMit(shape: RoofShape): SceneDocument {
  return {
    id: 'doc-1', projectId: 1, schemaVersion: 2, revision: 1, units: 'mm',
    settings: { gridSize: 100, snapEnabled: true, angleSnap: 15 },
    levels: [{ id: 'l1', name: 'DG', elevation: 0, defaultWallHeight: 2500, floorThickness: 200, sortOrder: 0 }],
    nodes: [], materials: [],
    roofs: [{
      id: 'r1', type: 'roof', levelId: 'l1', visible: true, locked: false, tags: [],
      createdAt: ISO, updatedAt: ISO,
      polygon: [{ x: 0, y: 0 }, { x: 10000, y: 0 }, { x: 10000, y: 8000 }, { x: 0, y: 8000 }],
      roofType: shape, neigungGrad: 35, firstAzimutGrad: 90, ueberstandMm: 400, traufhoeheMm: 6000,
    }],
    metadata: { createdAt: ISO, updatedAt: ISO },
  };
}

test('additiv: alle 8 RoofShapes validieren gegen das Schema (kein 422)', () => {
  for (const shape of ROOF_SHAPES) {
    assert.equal(sceneDocumentSchema.safeParse(docMit(shape)).success, true, `Form ${shape} muss gültig sein`);
  }
});

test('Regression: die 4 Alt-Formen bleiben gültig', () => {
  for (const shape of ['sattel', 'walm', 'pult', 'flach'] as RoofShape[]) {
    assert.equal(sceneDocumentSchema.safeParse(docMit(shape)).success, true);
  }
});

test('l-shape validiert jetzt (statt 422)', () => {
  const res = sceneDocumentSchema.safeParse(docMit('l-shape'));
  assert.equal(res.success, true);
});

test('unbekannte Dachform wird abgelehnt', () => {
  const doc = docMit('sattel');
  (doc.roofs[0] as { roofType: string }).roofType = 'kuppel';
  assert.equal(sceneDocumentSchema.safeParse(doc).success, false);
});

test('L/T/U render-neutral: dachMeshWelt liefert leeres Mesh statt Wurf/Crash', () => {
  for (const shape of VERSCHNEIDUNGS_FORMEN) {
    const roof = docMit(shape).roofs[0] as RoofNode;
    const mesh = dachMeshWelt(roof); // darf NICHT werfen
    assert.deepEqual(mesh.dreiecke, [], `${shape}: noch keine Flächen (Stufe 2)`);
    assert.equal(mesh.firstHoeheMm, 6000);
    assert.deepEqual(dachflaechen(roof), []);
  }
});

test('rechteckige Form (sattel) rendert unverändert weiter (Regression)', () => {
  const roof = docMit('sattel').roofs[0] as RoofNode;
  assert.ok(dachMeshWelt(roof).dreiecke.length > 0, 'Sattel liefert weiter Dreiecke');
  assert.ok(dachflaechen(roof).length >= 1, 'Sattel liefert weiter Aufbauflächen');
});

test('istVerschneidungsForm trennt L/T/U von den Rechteck-Formen', () => {
  assert.equal(istVerschneidungsForm('l-shape'), true);
  assert.equal(istVerschneidungsForm('t-shape'), true);
  assert.equal(istVerschneidungsForm('u-shape'), true);
  assert.equal(istVerschneidungsForm('sattel'), false);
  assert.equal(istVerschneidungsForm('rect'), false);
});
