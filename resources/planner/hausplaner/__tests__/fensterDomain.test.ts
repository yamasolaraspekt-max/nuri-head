/**
 * Domain: OpeningNode traegt additiv eine Fenster-Produktkonfiguration (produkt), die validiert.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { openingNodeSchema } from '../domain/validation';

const basis = {
  id: 'w1', type: 'window' as const, levelId: 'l1', visible: true, locked: false, tags: [] as string[],
  createdAt: '2026-01-01T00:00:00.000Z', updatedAt: '2026-01-01T00:00:00.000Z',
  hostWallId: 'wall1', offsetFromWallStart: 500, width: 1230, height: 1480, sillHeight: 900,
};

test('Fenster ohne produkt bleibt gueltig (additiv, optional)', () => {
  assert.ok(openingNodeSchema.safeParse(basis).success);
});

test('Fenster mit produkt (Profil/Glas/Oeffnungsart/RC) validiert', () => {
  const node = { ...basis, produkt: { profilId: 'kunststoff-82', verglasungId: '3fach-p4a', oeffnungsArt: 'dreh-kipp', rc: 'RC2' } };
  assert.ok(openingNodeSchema.safeParse(node).success);
});

test('ungueltige Oeffnungsart wird abgelehnt', () => {
  const node = { ...basis, produkt: { oeffnungsArt: 'schiebe' } };
  assert.equal(openingNodeSchema.safeParse(node).success, false);
});
