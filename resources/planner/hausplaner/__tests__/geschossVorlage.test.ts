/**
 * Geschoss als Vorlage duplizieren (Reuse). Reine, deterministische Kopier-Logik.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { dupliziereGeschoss, type LevelVorlage } from '../geometry/geschossVorlage';

interface TestNode {
  id: string;
  levelId: string;
  type: string;
  hostWallId?: string;
  extra?: number;
}

function idFolge(): () => string {
  let n = 0;
  return () => `neu-${++n}`;
}

const eg: LevelVorlage = {
  id: 'eg', name: 'EG', elevation: 0, defaultWallHeight: 2500, floorThickness: 200, sortOrder: 0,
};

test('neues Geschoss liegt ein Stockwerk höher und behält Aufbaumaße', () => {
  const { level } = dupliziereGeschoss(eg, [], null, idFolge(), 'OG');
  assert.equal(level.name, 'OG');
  assert.equal(level.elevation, 2700);
  assert.equal(level.sortOrder, 1);
  assert.equal(level.defaultWallHeight, 2500);
  assert.notEqual(level.id, 'eg');
});

test('alle Nodes bekommen neue IDs und hängen am neuen Geschoss', () => {
  const nodes: TestNode[] = [
    { id: 'w1', levelId: 'eg', type: 'wall', extra: 5 },
    { id: 'w2', levelId: 'eg', type: 'wall' },
  ];
  const { level, nodes: neu } = dupliziereGeschoss(eg, nodes, null, idFolge(), 'OG');
  assert.equal(neu.length, 2);
  for (const n of neu) {
    assert.notEqual(n.id, 'w1');
    assert.notEqual(n.id, 'w2');
    assert.equal(n.levelId, level.id);
  }
  assert.equal(neu[0].extra, 5);
});

test('Öffnung wird auf die NEUE Wirtswand umgehängt (id-Remap)', () => {
  const nodes: TestNode[] = [
    { id: 'w1', levelId: 'eg', type: 'wall' },
    { id: 'f1', levelId: 'eg', type: 'window', hostWallId: 'w1' },
  ];
  const { nodes: neu } = dupliziereGeschoss(eg, nodes, null, idFolge(), 'OG');
  const wand = neu.find((n) => n.type === 'wall')!;
  const fenster = neu.find((n) => n.type === 'window')!;
  assert.equal(fenster.hostWallId, wand.id);
  assert.notEqual(fenster.hostWallId, 'w1');
});

test('Dach wird mitkopiert (neue id, neues Geschoss)', () => {
  const dach = { id: 'd1', levelId: 'eg', roofType: 'sattel' };
  const { level, roof } = dupliziereGeschoss(eg, [], dach, idFolge(), 'OG');
  assert.ok(roof);
  assert.notEqual(roof!.id, 'd1');
  assert.equal(roof!.levelId, level.id);
  assert.equal((roof as typeof dach).roofType, 'sattel');
});

test('ohne Dach bleibt roof null', () => {
  const { roof } = dupliziereGeschoss(eg, [], null, idFolge(), 'OG');
  assert.equal(roof, null);
})

test('hängende hostWallId (Wirtswand nicht mitkopiert) wird gedroppt statt alte id zu behalten', () => {
  const nodes: TestNode[] = [
    { id: 'f1', levelId: 'eg', type: 'window', hostWallId: 'fehlt' },
  ];
  const { nodes: neu } = dupliziereGeschoss(eg, nodes, null, idFolge(), 'OG');
  const fenster = neu.find((n) => n.type === 'window')!;
  assert.equal(fenster.hostWallId, undefined); // gedroppt, NICHT 'fehlt'
});
