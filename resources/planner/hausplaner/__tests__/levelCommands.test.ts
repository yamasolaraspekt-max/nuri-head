/**
 * Geschoss-Commands (P2b-5): ADD_LEVEL / UPDATE_LEVEL / REMOVE_LEVEL.
 * Reine Regeln auf dem Immer-Draft — kein stilles Löschen von Fach-Daten.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { enablePatches, produceWithPatches } from 'immer';
import type { SceneDocument, Level, WallNode, RoofNode } from '../domain/scene.types';
import { applyCommand } from '../commands/applyCommand';
import { CommandAbgelehnt, type HausplanerCommand } from '../domain/commands.types';

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

function level(id: string, sortOrder: number, elevation: number): Level {
  return { id, name: id.toUpperCase(), elevation, defaultWallHeight: 2500, floorThickness: 200, sortOrder };
}

function wand(id: string, levelId: string): WallNode {
  return {
    id, type: 'wall', levelId, visible: true, locked: false, tags: [],
    createdAt: JETZT, updatedAt: JETZT,
    start: { x: 0, y: 0 }, end: { x: 4000, y: 0 }, thickness: 240, height: 2500,
  };
}

function anwenden(szene: SceneDocument, command: HausplanerCommand): SceneDocument {
  const [next] = produceWithPatches(szene, (draft) => {
    applyCommand(draft, command, JETZT);
  });
  return next;
}

test('ADD_LEVEL fügt Geschoss hinzu und sortiert nach sortOrder', () => {
  // Neues Level mit sortOrder 5 zuerst, dann eines mit sortOrder 2 → Reihenfolge egal, Ergebnis sortiert.
  let szene = leereSzene();
  szene = anwenden(szene, { type: 'ADD_LEVEL', level: level('dg', 5, 5000) });
  szene = anwenden(szene, { type: 'ADD_LEVEL', level: level('og', 2, 2500) });
  assert.deepEqual(szene.levels.map((l) => l.id), ['eg', 'og', 'dg']);
});

test('ADD_LEVEL lehnt doppelte id ab', () => {
  const szene = leereSzene();
  assert.throws(
    () => anwenden(szene, { type: 'ADD_LEVEL', level: level('eg', 1, 3000) }),
    (e: unknown) => e instanceof CommandAbgelehnt && e.grund === 'level_existiert',
  );
});

test('ADD_LEVEL lehnt nicht-ganzzahlige Geometrie ab', () => {
  const szene = leereSzene();
  const krumm: Level = { id: 'og', name: 'OG', elevation: 2500.5, defaultWallHeight: 2500, floorThickness: 200, sortOrder: 1 };
  assert.throws(
    () => anwenden(szene, { type: 'ADD_LEVEL', level: krumm }),
    (e: unknown) => e instanceof CommandAbgelehnt && e.grund === 'nicht_ganzzahlig',
  );
});

test('UPDATE_LEVEL benennt um und re-sortiert bei sortOrder-Änderung', () => {
  let szene = leereSzene();
  szene = anwenden(szene, { type: 'ADD_LEVEL', level: level('og', 1, 2500) });
  szene = anwenden(szene, { type: 'UPDATE_LEVEL', levelId: 'og', changes: { name: 'Obergeschoss', sortOrder: -1 } });
  const og = szene.levels.find((l) => l.id === 'og');
  assert.equal(og?.name, 'Obergeschoss');
  assert.deepEqual(szene.levels.map((l) => l.id), ['og', 'eg']); // sortOrder -1 vor 0
});

test('UPDATE_LEVEL ignoriert id-Änderung (Referenz-Wahrheit bleibt)', () => {
  let szene = leereSzene();
  // @ts-expect-error id ist nicht Teil von Partial<Level>-Absicht, wird aber defensiv ignoriert
  szene = anwenden(szene, { type: 'UPDATE_LEVEL', levelId: 'eg', changes: { id: 'neu', name: 'X' } });
  assert.equal(szene.levels.length, 1);
  assert.equal(szene.levels[0].id, 'eg');
  assert.equal(szene.levels[0].name, 'X');
});

test('UPDATE_LEVEL lehnt unbekanntes Level ab', () => {
  const szene = leereSzene();
  assert.throws(
    () => anwenden(szene, { type: 'UPDATE_LEVEL', levelId: 'gibtsnicht', changes: { name: 'X' } }),
    (e: unknown) => e instanceof CommandAbgelehnt && e.grund === 'level_unbekannt',
  );
});

test('REMOVE_LEVEL entfernt leeres Geschoss', () => {
  let szene = leereSzene();
  szene = anwenden(szene, { type: 'ADD_LEVEL', level: level('og', 1, 2500) });
  szene = anwenden(szene, { type: 'REMOVE_LEVEL', levelId: 'og' });
  assert.deepEqual(szene.levels.map((l) => l.id), ['eg']);
});

test('REMOVE_LEVEL lehnt das letzte Geschoss ab', () => {
  const szene = leereSzene();
  assert.throws(
    () => anwenden(szene, { type: 'REMOVE_LEVEL', levelId: 'eg' }),
    (e: unknown) => e instanceof CommandAbgelehnt && e.grund === 'level_letztes',
  );
});

test('REMOVE_LEVEL lehnt ein Geschoss mit Nodes ab (kein stilles Löschen)', () => {
  let szene = leereSzene();
  szene = anwenden(szene, { type: 'ADD_LEVEL', level: level('og', 1, 2500) });
  szene = anwenden(szene, { type: 'ADD_NODE', node: wand('w1', 'og') });
  assert.throws(
    () => anwenden(szene, { type: 'REMOVE_LEVEL', levelId: 'og' }),
    (e: unknown) => e instanceof CommandAbgelehnt && e.grund === 'level_nicht_leer',
  );
});

test('REMOVE_LEVEL lehnt ein Geschoss mit Dach ab', () => {
  let szene = leereSzene();
  szene = anwenden(szene, { type: 'ADD_LEVEL', level: level('og', 1, 2500) });
  const dach: RoofNode = {
    id: 'd1', type: 'roof', levelId: 'og', visible: true, locked: false, tags: [],
    createdAt: JETZT, updatedAt: JETZT,
    polygon: [{ x: 0, y: 0 }, { x: 4000, y: 0 }, { x: 4000, y: 4000 }, { x: 0, y: 4000 }],
    roofType: 'sattel', neigungGrad: 30, firstAzimutGrad: 0, ueberstandMm: 500, traufhoeheMm: 2500,
  };
  szene = anwenden(szene, { type: 'ADD_ROOF', roof: dach });
  assert.throws(
    () => anwenden(szene, { type: 'REMOVE_LEVEL', levelId: 'og' }),
    (e: unknown) => e instanceof CommandAbgelehnt && e.grund === 'level_nicht_leer',
  );
});
