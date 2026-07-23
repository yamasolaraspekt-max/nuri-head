import { test } from 'node:test';
import assert from 'node:assert/strict';
import { useHausplanerStore } from '../store/hausplanerStore';
import { fensterBauartNach } from '../geometry/oeffnungsBauarten';
import type { SceneDocument, WallNode, OpeningNode, SceneNode } from '../domain/scene.types';

const JETZT = '2026-07-23T12:00:00.000Z';
function szeneMitWand(): SceneDocument {
  const wall: WallNode = {
    id: 'w1', type: 'wall', levelId: 'eg', visible: true, locked: false, tags: [],
    createdAt: JETZT, updatedAt: JETZT, start: { x: 0, y: 0 }, end: { x: 6000, y: 0 },
    thickness: 240, height: 2500,
  };
  return {
    id: 's', projectId: 1, schemaVersion: 2, revision: 1, units: 'mm',
    settings: { gridSize: 100, snapEnabled: true, angleSnap: 15 },
    levels: [{ id: 'eg', name: 'EG', elevation: 0, defaultWallHeight: 2500, floorThickness: 200, sortOrder: 0 }],
    nodes: [wall], roofs: [], materials: [], metadata: { createdAt: JETZT, updatedAt: JETZT },
  } as unknown as SceneDocument;
}

test('ConfigWizard-Schreiblogik: Fenster mit Bauart landet als OpeningNode auf der Wand', () => {
  const store = useHausplanerStore;
  store.getState().init(szeneMitWand(), '', '');
  const scene0 = store.getState().scene!;
  const wand = scene0.nodes.find((n): n is WallNode => n.type === 'wall')!;
  // exakt die Konstruktion aus ConfigWizard nachbilden
  const len = Math.hypot(wand.end.x - wand.start.x, wand.end.y - wand.start.y);
  const w = Math.min(1010, Math.max(100, Math.round(len - 100)));
  const offset = Math.max(0, Math.round(len / 2 - w / 2));
  const knoten: OpeningNode = {
    id: 'neu-fenster', type: 'window', levelId: wand.levelId, visible: true, locked: false, tags: [],
    createdAt: JETZT, updatedAt: JETZT, hostWallId: wand.id, offsetFromWallStart: offset,
    width: w, height: 1360, sillHeight: 900,
    produkt: { typ: '05_dreh_kipp_links', oeffnungsArt: fensterBauartNach('05_dreh_kipp_links')?.oeffnungsArt },
  };
  const ok = store.getState().executeCommand({ type: 'ADD_NODE', node: knoten as SceneNode });
  assert.equal(ok, true, 'ADD_NODE akzeptiert');
  const scene1 = store.getState().scene!;
  const fenster = scene1.nodes.filter((n) => n.type === 'window');
  assert.equal(fenster.length, 1);
  const f = fenster[0] as OpeningNode;
  assert.equal(f.hostWallId, 'w1');
  assert.equal(f.produkt?.typ, '05_dreh_kipp_links');
  assert.equal(f.produkt?.oeffnungsArt, 'dreh-kipp');
  assert.equal(store.getState().speicherStatus, 'ungespeichert');
});
