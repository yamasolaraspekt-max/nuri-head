/**
 * Decke (Feature A) — additiv/kein 422, Commands (max 1/Level, mm-Invariante), Treppendurchbruch (aus
 * Grundriss), Slab-Nettofläche (Loch reduziert), Etagen-Stapel (eine Ableitung).
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { applyCommand } from '../commands/applyCommand';
import { sceneDocumentSchema } from '../domain/validation';
import { deckenNettoFlaecheM2, naechsteEtageElevationMm } from '../renderers/three-d/deckenMesh';
import { treppeZuParametern } from '../geometry/treppeObjekt';
import type { SceneDocument, CeilingNode, ObjectNode, Level } from '../domain/scene.types';

const ISO = '2026-07-23T00:00:00.000Z';
const LEVEL: Level = { id: 'l1', name: 'EG', elevation: 0, defaultWallHeight: 2500, floorThickness: 200, sortOrder: 0 };
const UMRISS = [{ x: 0, y: 0 }, { x: 10000, y: 0 }, { x: 10000, y: 8000 }, { x: 0, y: 8000 }];

function baseDoc(): SceneDocument {
  return {
    id: 'd', projectId: 1, schemaVersion: 2, revision: 1, units: 'mm',
    settings: { gridSize: 100, snapEnabled: true, angleSnap: 15 },
    levels: [LEVEL], nodes: [], materials: [], roofs: [], metadata: { createdAt: ISO, updatedAt: ISO },
  };
}
function decke(over: Partial<CeilingNode> = {}): CeilingNode {
  return {
    id: 'c1', type: 'ceiling', levelId: 'l1', visible: true, locked: false, tags: [],
    createdAt: ISO, updatedAt: ISO, polygon: UMRISS, dickeMm: 200, ...over,
  };
}
function treppe(): ObjectNode {
  return {
    id: 's1', type: 'object', objectType: 'stair', levelId: 'l1', visible: true, locked: false, tags: [],
    createdAt: ISO, updatedAt: ISO, catalogItemId: 'stair',
    transform: { position: { x: 0, y: 0, z: 0 }, rotation: { x: 0, y: 0, z: 0 }, scale: { x: 1, y: 1, z: 1 } },
    parameters: treppeZuParametern({ startX: 2000, startY: 2000, endX: 5000, endY: 2000, laufbreite: 1000, geschosshoehe: 2600, bereich: 'wohnung' }),
  };
}

test('additiv: Dokument OHNE ceilings validiert (kein 422); MIT ceilings ebenfalls', () => {
  assert.equal(sceneDocumentSchema.safeParse(baseDoc()).success, true);
  const doc = baseDoc();
  doc.ceilings = [decke()];
  assert.equal(sceneDocumentSchema.safeParse(doc).success, true);
});

test('ADD_CEILING legt eine Decke an; zweite je Level wird abgelehnt (max. 1)', () => {
  const doc = baseDoc();
  applyCommand(doc, { type: 'ADD_CEILING', ceiling: decke() }, ISO);
  assert.equal(doc.ceilings?.length, 1);
  assert.throws(() => applyCommand(doc, { type: 'ADD_CEILING', ceiling: decke({ id: 'c2' }) }, ISO), /bereits eine Decke/);
});

test('mm-Invariante: nicht-ganzzahlige Deckendicke wird abgelehnt', () => {
  const doc = baseDoc();
  assert.throws(() => applyCommand(doc, { type: 'ADD_CEILING', ceiling: decke({ dickeMm: 200.5 }) }, ISO), /ganzen Millimetern/);
});

test('Treppendurchbruch (aus Grundriss): Treppe im Level ⇒ automatische Öffnung in der Decke', () => {
  const doc = baseDoc();
  doc.nodes = [treppe()];
  applyCommand(doc, { type: 'ADD_CEILING', ceiling: decke() }, ISO); // keine oeffnungen gesetzt
  const c = doc.ceilings?.[0];
  assert.ok(c?.oeffnungen && c.oeffnungen.length >= 1, 'Treppe erzeugt einen Durchbruch');
  // Der Durchbruch reduziert die Netto-Deckenfläche.
  assert.ok(deckenNettoFlaecheM2(c!) < deckenNettoFlaecheM2(decke()), 'Loch verkleinert die Slab-Fläche');
});

test('deckenNettoFlaecheM2: Umriss minus Durchbrüche', () => {
  const brutto = deckenNettoFlaecheM2(decke());
  assert.ok(Math.abs(brutto - 80) < 0.01, 'Umriss 10×8 = 80 m²');
  const mitLoch = deckenNettoFlaecheM2(decke({ oeffnungen: [{ polygon: [{ x: 1000, y: 1000 }, { x: 3000, y: 1000 }, { x: 3000, y: 2000 }, { x: 1000, y: 2000 }] }] }));
  assert.ok(Math.abs(mitLoch - 78) < 0.01, 'minus 2×1 = 78 m²');
});

test('UPDATE/REMOVE_CEILING', () => {
  const doc = baseDoc();
  applyCommand(doc, { type: 'ADD_CEILING', ceiling: decke() }, ISO);
  applyCommand(doc, { type: 'UPDATE_CEILING', ceilingId: 'c1', changes: { dickeMm: 250 } }, ISO);
  assert.equal(doc.ceilings?.[0].dickeMm, 250);
  applyCommand(doc, { type: 'REMOVE_CEILING', ceilingId: 'c1' }, ISO);
  assert.equal(doc.ceilings?.length, 0);
  assert.throws(() => applyCommand(doc, { type: 'REMOVE_CEILING', ceilingId: 'x' }, ISO), /existiert nicht/);
});

test('Etagen-Stapel: nächste Elevation = Elevation + Wandhöhe + Deckendicke (eine Ableitung)', () => {
  assert.equal(naechsteEtageElevationMm(LEVEL, decke()), 0 + 2500 + 200);
  // ohne Decke: Rückfall auf floorThickness (kein Rateswert der Höhe)
  assert.equal(naechsteEtageElevationMm(LEVEL, undefined), 0 + 2500 + 200);
});
