import { test } from 'node:test';
import assert from 'node:assert/strict';
import { migriereSzene, sceneDocumentSchema } from '../domain/validation';

const JETZT = '2026-07-19T12:00:00.000Z';

function studioSzene(projectId: number): Record<string, unknown> {
  return {
    id: 'studio-scratch',
    projectId,
    schemaVersion: 1,
    revision: 1,
    units: 'mm',
    settings: { gridSize: 100, snapEnabled: true, angleSnap: 15 },
    levels: [{
      id: 'level-eg',
      name: 'Erdgeschoss',
      elevation: 0,
      defaultWallHeight: 2500,
      floorThickness: 200,
      sortOrder: 0,
    }],
    nodes: [],
    materials: [],
    metadata: { createdAt: JETZT, updatedAt: JETZT },
  };
}

test('Studio-Scratch-Szene mit positiver projectId migriert von v1 nach v2 und validiert', () => {
  const ergebnis = sceneDocumentSchema.safeParse(migriereSzene(studioSzene(999999999)));

  assert.equal(ergebnis.success, true);
  if (!ergebnis.success) {
    return;
  }

  assert.equal(ergebnis.data.projectId, 999999999);
  assert.equal(ergebnis.data.schemaVersion, 3); // Z-06-N1: Migrationsziel, war 2
  assert.deepEqual(ergebnis.data.roofs, []);
});

test('Studio-Scratch-Szene mit projectId 0 bleibt am Zod-Vertrag rot', () => {
  const ergebnis = sceneDocumentSchema.safeParse(migriereSzene(studioSzene(0)));

  assert.equal(ergebnis.success, false);
  if (ergebnis.success) {
    return;
  }

  assert.equal(
    ergebnis.error.issues.some((issue) => issue.path.length === 1 && issue.path[0] === 'projectId'),
    true,
  );
});
