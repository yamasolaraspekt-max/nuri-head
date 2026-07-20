import { test } from 'node:test';
import assert from 'node:assert/strict';
import { sceneDocumentSchema, validateSceneIntegrity } from '../domain/validation';

const JETZT = '2026-07-19T00:00:00.000Z';

function szene(): Record<string, any> {
  return {
    id: 'doc-502',
    projectId: 502,
    schemaVersion: 2,
    revision: 1,
    units: 'mm',
    settings: { gridSize: 100, snapEnabled: true, angleSnap: 15 },
    levels: [{ id: 'L', name: 'EG', elevation: 0, defaultWallHeight: 2500, floorThickness: 200, sortOrder: 0 }],
    nodes: [{
      id: 'w1', type: 'wall', levelId: 'L', visible: true, locked: false, tags: [],
      createdAt: JETZT, updatedAt: JETZT,
      start: { x: 0, y: 0 }, end: { x: 8000, y: 0 }, thickness: 240, height: 2500,
    }],
    materials: [],
    roofs: [{
      id: 'r1', type: 'roof', levelId: 'L', visible: true, locked: false, tags: [],
      createdAt: JETZT, updatedAt: JETZT,
      polygon: [{ x: 0, y: 0 }, { x: 8000, y: 0 }, { x: 8000, y: 10000 }, { x: 0, y: 10000 }],
      roofType: 'sattel', neigungGrad: 35, firstAzimutGrad: 90, ueberstandMm: 500, traufhoeheMm: 2500,
    }],
    metadata: { createdAt: JETZT, updatedAt: JETZT },
  };
}

function parseUndPruefe(scene: Record<string, any>): boolean {
  const geparst = sceneDocumentSchema.safeParse(scene);
  return geparst.success && validateSceneIntegrity(geparst.data).length === 0;
}

test('v2-Dachszene ist ueber denselben Ladepfad vollstaendig ladbar', () => {
  assert.equal(parseUndPruefe(szene()), true);
});

test('Zod-Vertrag verwirft Zukunftsfeld, Float-mm und unbekannten Node', () => {
  const zukunft = szene();
  zukunft.zukunft_v3_probe = true;
  assert.equal(parseUndPruefe(zukunft), false);

  const floatMm = szene();
  floatMm.nodes[0].end.x = 8000.5;
  assert.equal(parseUndPruefe(floatMm), false);

  const unbekannt = szene();
  unbekannt.nodes[0].type = 'mystery';
  assert.equal(parseUndPruefe(unbekannt), false);
});

test('Zod plus Integritaet verwirft Nullwand und ungueltige Oeffnungen', () => {
  const nullwand = szene();
  nullwand.nodes[0].end = { ...nullwand.nodes[0].start };
  assert.equal(parseUndPruefe(nullwand), false);

  const verwaist = szene();
  verwaist.nodes.push({
    id: 'f1', type: 'window', levelId: 'L', visible: true, locked: false, tags: [],
    createdAt: JETZT, updatedAt: JETZT, hostWallId: 'nicht-da',
    offsetFromWallStart: 1000, width: 1200, height: 1400, sillHeight: 900,
  });
  assert.equal(parseUndPruefe(verwaist), false);

  const ueberstehend = szene();
  ueberstehend.nodes.push({
    id: 'f1', type: 'window', levelId: 'L', visible: true, locked: false, tags: [],
    createdAt: JETZT, updatedAt: JETZT, hostWallId: 'w1',
    offsetFromWallStart: 7500, width: 1200, height: 1400, sillHeight: 900,
  });
  assert.equal(parseUndPruefe(ueberstehend), false);
});
