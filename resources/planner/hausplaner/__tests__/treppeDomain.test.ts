/**
 * Domain: ObjectNode mit objectType 'stair' ist gültig (additive Enum-Erweiterung) und trägt
 * seine Treppen-Fachdaten im parameters-Record (Roundtrip über treppeObjekt).
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { objectNodeSchema } from '../domain/validation';
import { treppeZuParametern, parametereZuTreppe } from '../geometry/treppeObjekt';

const basis = {
  id: 'stair-1',
  type: 'object' as const,
  levelId: 'lvl-1',
  visible: true,
  locked: false,
  tags: [] as string[],
  createdAt: '2026-01-01T00:00:00.000Z',
  updatedAt: '2026-01-01T00:00:00.000Z',
  catalogItemId: 'treppe-standard',
  transform: {
    position: { x: 0, y: 0, z: 0 },
    rotation: { x: 0, y: 0, z: 0 },
    scale: { x: 1, y: 1, z: 1 },
  },
};

test("objectNodeSchema akzeptiert objectType 'stair'", () => {
  const node = {
    ...basis,
    objectType: 'stair',
    parameters: treppeZuParametern({
      startX: 0, startY: 0, endX: 3000, endY: 0,
      laufbreite: 1000, geschosshoehe: 2800, bereich: 'wohnung',
    }),
  };
  const r = objectNodeSchema.safeParse(node);
  assert.ok(r.success, r.success ? '' : JSON.stringify(r.error.issues));
});

test('Treppen-Parameter überleben die Validierung (Roundtrip nach Parse)', () => {
  const params = treppeZuParametern({
    startX: 100, startY: 200, endX: 3100, endY: 200,
    laufbreite: 1000, geschosshoehe: 2800, bereich: 'gebaeude', gewuenschteSteigung: 180,
  });
  const node = { ...basis, objectType: 'stair', parameters: params };
  const r = objectNodeSchema.safeParse(node);
  assert.ok(r.success);
  const back = parametereZuTreppe((r as { data: typeof node }).data.parameters);
  assert.equal(back!.bereich, 'gebaeude');
  assert.equal(back!.gewuenschteSteigung, 180);
});
