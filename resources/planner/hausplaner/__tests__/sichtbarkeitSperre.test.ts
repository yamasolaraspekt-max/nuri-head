import { test } from 'node:test';
import assert from 'node:assert/strict';
import { enablePatches, produceWithPatches } from 'immer';
import type { SceneDocument, WallNode } from '../domain/scene.types';
import { applyCommand } from '../commands/applyCommand';
import { CommandAbgelehnt, type HausplanerCommand } from '../domain/commands.types';

enablePatches();
const JETZT = '2026-07-23T12:00:00.000Z';

function wand(id: string, ex = 4000): WallNode {
  return { id, type: 'wall', levelId: 'eg', visible: true, locked: false, tags: [],
    createdAt: JETZT, updatedAt: JETZT, start: { x: 0, y: 0 }, end: { x: ex, y: 0 }, thickness: 240, height: 2500 };
}
function szene(): SceneDocument {
  return { id: 'doc-test', projectId: 1, schemaVersion: 1, revision: 1, units: 'mm',
    settings: { gridSize: 100, snapEnabled: true, angleSnap: 15 },
    levels: [{ id: 'eg', name: 'EG', elevation: 0, defaultWallHeight: 2500, floorThickness: 200, sortOrder: 0 }],
    nodes: [wand('w1'), wand('w2', 3000)], materials: [], metadata: { createdAt: JETZT, updatedAt: JETZT } };
}
function aus(scene: SceneDocument, command: HausplanerCommand): SceneDocument {
  const [next] = produceWithPatches(scene, (d) => applyCommand(d, command, JETZT));
  return next as SceneDocument;
}
const byId = (s: SceneDocument, id: string) => s.nodes.find((n) => n.id === id)!;

test('SET_NODES_SICHTBAR blendet mehrere aus und einzeln wieder ein', () => {
  const s1 = aus(szene(), { type: 'SET_NODES_SICHTBAR', nodeIds: ['w1', 'w2'], sichtbar: false });
  assert.equal(byId(s1, 'w1').visible, false);
  assert.equal(byId(s1, 'w2').visible, false);
  const s2 = aus(s1, { type: 'SET_NODES_SICHTBAR', nodeIds: ['w1'], sichtbar: true });
  assert.equal(byId(s2, 'w1').visible, true);
  assert.equal(byId(s2, 'w2').visible, false);
});
test('SET_NODES_GESPERRT sperrt und gibt frei', () => {
  const s1 = aus(szene(), { type: 'SET_NODES_GESPERRT', nodeIds: ['w1'], gesperrt: true });
  assert.equal(byId(s1, 'w1').locked, true);
  const s2 = aus(s1, { type: 'SET_NODES_GESPERRT', nodeIds: ['w1'], gesperrt: false });
  assert.equal(byId(s2, 'w1').locked, false);
});
test('unbekannte Node-ID wird abgelehnt', () => {
  assert.throws(() => aus(szene(), { type: 'SET_NODES_SICHTBAR', nodeIds: ['xxx'], sichtbar: false }), CommandAbgelehnt);
});
test('3D-Sichtbarkeitsvertrag: visible===false wird vom Renderer gefiltert', () => {
  const s1 = aus(szene(), { type: 'SET_NODES_SICHTBAR', nodeIds: ['w1'], sichtbar: false });
  const sichtbar = s1.nodes.filter((n) => n.levelId === 'eg' && n.visible !== false);
  assert.equal(sichtbar.length, 1);
  assert.equal(sichtbar[0].id, 'w2');
});
