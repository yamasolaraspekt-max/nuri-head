import { test } from 'node:test';
import assert from 'node:assert/strict';
import { enablePatches, produceWithPatches, applyPatches } from 'immer';
import type { SceneDocument, WallNode, OpeningNode } from '../domain/scene.types';
import { applyCommand } from '../commands/applyCommand';
import { CommandAbgelehnt, type HausplanerCommand } from '../domain/commands.types';
import { Historie } from '../store/history';

enablePatches();

const JETZT = '2026-07-16T12:00:00.000Z';

function leereSzene(): SceneDocument {
  return {
    id: 'doc-test',
    projectId: 1,
    schemaVersion: 1,
    revision: 1,
    units: 'mm',
    settings: { gridSize: 100, snapEnabled: true, angleSnap: 15 },
    levels: [{ id: 'eg', name: 'EG', elevation: 0, defaultWallHeight: 2500, floorThickness: 200, sortOrder: 0 }],
    nodes: [],
    materials: [],
    metadata: { createdAt: JETZT, updatedAt: JETZT },
  };
}

function wand(id: string, ex = 4000): WallNode {
  return {
    id, type: 'wall', levelId: 'eg', visible: true, locked: false, tags: [],
    createdAt: JETZT, updatedAt: JETZT,
    start: { x: 0, y: 0 }, end: { x: ex, y: 0 }, thickness: 240, height: 2500,
  };
}

function fenster(id: string, wallId: string, offset = 1000, width = 1200): OpeningNode {
  return {
    id, type: 'window', levelId: 'eg', visible: true, locked: false, tags: [],
    createdAt: JETZT, updatedAt: JETZT,
    hostWallId: wallId, offsetFromWallStart: offset, width, height: 1400, sillHeight: 900,
  };
}

function fuehreAus(scene: SceneDocument, command: HausplanerCommand) {
  return produceWithPatches(scene, (draft) => applyCommand(draft, command, JETZT));
}

test('ADD_NODE Wand + Öffnung; Öffnung über Wandende wird abgelehnt', () => {
  let [szene] = fuehreAus(leereSzene(), { type: 'ADD_NODE', node: wand('w1') });
  [szene] = fuehreAus(szene, { type: 'ADD_NODE', node: fenster('f1', 'w1') });
  assert.equal(szene.nodes.length, 2);

  assert.throws(
    () => fuehreAus(szene, { type: 'ADD_NODE', node: fenster('f2', 'w1', 3500, 1200) }),
    (e: unknown) => e instanceof CommandAbgelehnt && e.grund === 'oeffnung_passt_nicht',
  );
});

test('Kante Mini-Wand: Länge 0 und kürzer als Dicke werden abgelehnt', () => {
  assert.throws(
    () => fuehreAus(leereSzene(), { type: 'ADD_NODE', node: wand('w0', 0) }),
    (e: unknown) => e instanceof CommandAbgelehnt && e.grund === 'wand_zu_kurz',
  );
  assert.throws(
    () => fuehreAus(leereSzene(), { type: 'ADD_NODE', node: wand('w0', 100) }), // 100 < Dicke 240
    (e: unknown) => e instanceof CommandAbgelehnt && e.grund === 'wand_zu_kurz',
  );
});

test('mm-Invariante: nicht-ganzzahlige Geometrie wird abgelehnt', () => {
  const krumm = wand('wk');
  krumm.end = { x: 4000.5 as number, y: 0 };
  assert.throws(
    () => fuehreAus(leereSzene(), { type: 'ADD_NODE', node: krumm }),
    (e: unknown) => e instanceof CommandAbgelehnt && e.grund === 'nicht_ganzzahlig',
  );
});

test('Wand verlängern hält den Öffnungs-Offset konstant (Abnahmekriterium 4)', () => {
  let [szene] = fuehreAus(leereSzene(), { type: 'ADD_NODE', node: wand('w1') });
  [szene] = fuehreAus(szene, { type: 'ADD_NODE', node: fenster('f1', 'w1', 1000, 1200) });

  [szene] = fuehreAus(szene, { type: 'MOVE_NODE', nodeId: 'w1', position: { start: { x: 0, y: 0 }, end: { x: 4500, y: 0 } } });
  const f = szene.nodes.find((n) => n.id === 'f1') as OpeningNode;
  assert.equal(f.offsetFromWallStart, 1000);
  assert.notEqual(f.clamped, true);
});

test('Entscheid (a): Wand kürzen unter den Offset ⇒ Öffnung klemmt ans Ende + clamped', () => {
  let [szene] = fuehreAus(leereSzene(), { type: 'ADD_NODE', node: wand('w1') });
  [szene] = fuehreAus(szene, { type: 'ADD_NODE', node: fenster('f1', 'w1', 2500, 1200) });

  [szene] = fuehreAus(szene, { type: 'MOVE_NODE', nodeId: 'w1', position: { start: { x: 0, y: 0 }, end: { x: 3000, y: 0 } } });
  const f = szene.nodes.find((n) => n.id === 'f1') as OpeningNode;
  assert.equal(f.offsetFromWallStart, 1800); // 3000 − 1200
  assert.equal(f.clamped, true);
});

test('Entscheid (a): Wand kürzer als die Öffnung ⇒ Command ABGELEHNT, Szene unverändert', () => {
  let [szene] = fuehreAus(leereSzene(), { type: 'ADD_NODE', node: wand('w1') });
  [szene] = fuehreAus(szene, { type: 'ADD_NODE', node: fenster('f1', 'w1', 1000, 1200) });

  assert.throws(
    () => fuehreAus(szene, { type: 'MOVE_NODE', nodeId: 'w1', position: { start: { x: 0, y: 0 }, end: { x: 1000, y: 0 } } }),
    (e: unknown) => e instanceof CommandAbgelehnt && e.grund === 'oeffnung_passt_nicht',
  );
  const w = szene.nodes.find((n) => n.id === 'w1') as WallNode;
  assert.equal(w.end.x, 4000, 'Ablehnung darf die Szene nicht verändern');
});

test('Kante: Wand löschen entfernt ihre Öffnungen — EIN Undo stellt beides wieder her (Abnahmekriterium 3/Kaskade)', () => {
  let [szene] = fuehreAus(leereSzene(), { type: 'ADD_NODE', node: wand('w1') });
  [szene] = fuehreAus(szene, { type: 'ADD_NODE', node: fenster('f1', 'w1') });

  const [nachLoeschen, , inverse] = fuehreAus(szene, { type: 'REMOVE_NODE', nodeId: 'w1' });
  assert.equal(nachLoeschen.nodes.length, 0, 'Wand UND Öffnung weg');

  const wiederhergestellt = applyPatches(nachLoeschen, inverse) as SceneDocument;
  assert.equal(wiederhergestellt.nodes.length, 2, 'EIN Undo bringt Wand und Öffnung zurück');
});

test('Undo/Redo über die Historie: 3 Commands, 3× undo ⇒ Ausgangszustand byte-identisch; 3× redo ⇒ Endzustand', () => {
  const historie = new Historie();
  let szene = leereSzene();
  const start = JSON.stringify(szene);

  const commands: HausplanerCommand[] = [
    { type: 'ADD_NODE', node: wand('w1') },
    { type: 'ADD_NODE', node: fenster('f1', 'w1') },
    { type: 'UPDATE_SETTINGS', changes: { gridSize: 50 } },
  ];
  for (const c of commands) {
    const [neu, patches, inversePatches] = fuehreAus(szene, c);
    historie.push({ patches, inversePatches, beschreibung: c.type });
    szene = neu;
  }
  const ende = JSON.stringify(szene);

  for (let i = 0; i < 3; i++) {
    szene = applyPatches(szene, historie.undo()!.inversePatches) as SceneDocument;
  }
  assert.equal(JSON.stringify(szene), start, 'Undo ×3 ⇒ byte-identischer Start');

  for (let i = 0; i < 3; i++) {
    szene = applyPatches(szene, historie.redo()!.patches) as SceneDocument;
  }
  assert.equal(JSON.stringify(szene), ende, 'Redo ×3 ⇒ byte-identisches Ende');
});

test('Kante Save-Grenze: markiereGespeichert leert die Historie nicht; Undo nach Save ⇒ dirty', () => {
  const historie = new Historie();
  let szene = leereSzene();
  const [neu, patches, inversePatches] = fuehreAus(szene, { type: 'ADD_NODE', node: wand('w1') });
  historie.push({ patches, inversePatches, beschreibung: 'ADD_NODE' });
  szene = neu;

  historie.markiereGespeichert();
  assert.equal(historie.istDirty(), false);
  assert.equal(historie.kannUndo(), true, 'Speichern leert die Historie nicht');

  historie.undo();
  assert.equal(historie.istDirty(), true, 'Undo hinter die Save-Grenze ⇒ dirty');

  historie.redo();
  assert.equal(historie.istDirty(), false, 'Redo zurück auf die Save-Grenze ⇒ sauber');
});

test('neues Command nach Undo verwirft den Redo-Stapel', () => {
  const historie = new Historie();
  historie.push({ patches: [], inversePatches: [], beschreibung: 'a' });
  historie.push({ patches: [], inversePatches: [], beschreibung: 'b' });
  historie.undo();
  assert.equal(historie.kannRedo(), true);
  historie.push({ patches: [], inversePatches: [], beschreibung: 'c' });
  assert.equal(historie.kannRedo(), false);
});
