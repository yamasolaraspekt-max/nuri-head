/**
 * D-a — Dach-Datenmodell: RoofNode + schemaVersion 2 + Lade-Migration v1→v2 + Dach-Commands.
 * Grundlage: docs/hausplaner/dach-andock-spec.md §1 (▲D1) + §3 (Kanten) + §4 (Abnahme).
 * Läuft im node-Testrunner (strip-types); kein Build nötig.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { enablePatches, produceWithPatches, applyPatches } from 'immer';
import type { SceneDocument, RoofNode, WallNode } from '../domain/scene.types';
import { applyCommand } from '../commands/applyCommand';
import { CommandAbgelehnt, type HausplanerCommand } from '../domain/commands.types';
import { sceneDocumentSchema, migriereSzene } from '../domain/validation';

enablePatches();

const JETZT = '2026-07-17T12:00:00.000Z';

function leereSzene(): SceneDocument {
  return {
    id: 'doc-test',
    projectId: 1,
    schemaVersion: 3,
    revision: 1,
    units: 'mm',
    settings: { gridSize: 100, snapEnabled: true, angleSnap: 15 },
    levels: [{ id: 'eg', name: 'EG', elevation: 0, defaultWallHeight: 2500, floorThickness: 200, sortOrder: 0 }],
    nodes: [],
    materials: [],
    roofs: [],
    metadata: { createdAt: JETZT, updatedAt: JETZT },
  };
}

function dach(id = 'r1', levelId = 'eg'): RoofNode {
  return {
    id,
    type: 'roof',
    levelId,
    visible: true,
    locked: false,
    tags: [],
    createdAt: JETZT,
    updatedAt: JETZT,
    polygon: [
      { x: 0, y: 0 },
      { x: 8000, y: 0 },
      { x: 8000, y: 10000 },
      { x: 0, y: 10000 },
    ],
    roofType: 'sattel',
    neigungGrad: 35,
    firstAzimutGrad: 90,
    ueberstandMm: 500,
    traufhoeheMm: 2500,
    // Z-06-N1: Pflichtfelder ab v3. Testgeometrie ist von Hand gesetzt ⇒ manuell/bestaetigt.
    geometrieHerkunft: 'manuell',
    freigabe: 'bestaetigt',
  };
}

function wand(id = 'w1'): WallNode {
  return {
    id, type: 'wall', levelId: 'eg', visible: true, locked: false, tags: [],
    createdAt: JETZT, updatedAt: JETZT,
    start: { x: 0, y: 0 }, end: { x: 8000, y: 0 }, thickness: 240, height: 2500,
  };
}

function fuehreAus(scene: SceneDocument, command: HausplanerCommand) {
  return produceWithPatches(scene, (draft) => applyCommand(draft, command, JETZT));
}

// ---- Migration v1 → v2 (▲D1, Kante 4) ----

// **Z-06-N1: aus v1→v2 wird v1→v3.** Die Zusage prüfte, dass die Migration auf 2 hebt; das Ziel
// ist jetzt 3. *Die tragende Aussage bleibt Wort für Wort dieselbe — „sonst UNVERÄNDERT" — und
// sie wird unten um die zwei neuen Felder ERWEITERT, nicht ersetzt.* Kein Vergleich ist
// weggefallen; die Liste der byte-gleichen Felder steht vollständig wie zuvor.
test('Migration v1→v3: setzt schemaVersion 3 + roofs [], sonst UNVERÄNDERT', () => {
  const v1 = {
    id: 'doc-v1',
    projectId: 7,
    schemaVersion: 1,
    revision: 3,
    units: 'mm',
    settings: { gridSize: 100, snapEnabled: true, angleSnap: 15 },
    levels: [{ id: 'eg', name: 'EG', elevation: 0, defaultWallHeight: 2500, floorThickness: 200, sortOrder: 0 }],
    nodes: [wand('w1')],
    materials: [{ id: 'm1', name: 'Kalksandstein' }],
    metadata: { createdAt: JETZT, updatedAt: JETZT },
  };

  const m = migriereSzene(v1) as Record<string, unknown>;

  assert.equal(m.schemaVersion, 3);
  assert.deepEqual(m.roofs, []);
  // Kein stilles Umschreiben von Bestand: alle anderen Felder byte-gleich.
  assert.equal(m.id, v1.id);
  assert.equal(m.projectId, v1.projectId);
  assert.equal(m.revision, v1.revision);
  assert.deepEqual(m.levels, v1.levels);
  assert.deepEqual(m.nodes, v1.nodes);
  assert.deepEqual(m.materials, v1.materials);
  assert.deepEqual(m.settings, v1.settings);
  assert.deepEqual(m.metadata, v1.metadata);
  // Original bleibt unangetastet (reine Funktion).
  assert.equal((v1 as Record<string, unknown>).roofs, undefined);
  assert.equal(v1.schemaVersion, 1);
});

test('Z-06-N1/K-06: die Migration setzt Bestand auf `zu_pruefen`, NICHT auf `bestaetigt`', () => {
  // **Die schärfste Zusage dieses Blattes.** Wer Bestandsgeometrie auf `bestaetigt` migriert,
  // macht die Migration grün und B10 im selben Zug wirkungslos: ab dann sieht jede geerbte
  // Näherung aus wie eine, auf die jemand geschaut hat. *Ein Status, den niemand vergeben hat,
  // ist kein Status.*
  const v2 = {
    ...leereSzene(),
    schemaVersion: 2, // ← eine ALTE Datei; genau darum geht es
    ceilings: [{ id: 'c1', type: 'ceiling', levelId: 'eg', visible: true, locked: false, tags: [],
      createdAt: JETZT, updatedAt: JETZT,
      polygon: [{ x: 0, y: 0 }, { x: 5000, y: 0 }, { x: 5000, y: 4000 }], dickeMm: 200 }],
    // **Ohne die zwei Felder — sonst prüft die Zusage ihre eigene Vorgabe.** Der Helfer `dach()`
    // trägt sie seit heute; beim ersten Anlauf stand hier `{ ...dach() }` und die Zusage fiel mit
    // `bestaetigt` statt `zu_pruefen`. *Sie hatte recht: ein Bestands-Test braucht Bestand.*
    roofs: [(({ geometrieHerkunft, freigabe, ...rest }) => rest)(dach())],
  } as Record<string, unknown>;

  const m = migriereSzene(v2) as Record<string, any>;

  assert.equal(m.ceilings[0].freigabe, 'zu_pruefen');
  assert.equal(m.ceilings[0].geometrieHerkunft, 'abgeleitet');
  assert.notEqual(m.ceilings[0].freigabe, 'bestaetigt');
  assert.equal(m.roofs[0].freigabe, 'zu_pruefen');
  // Additiv: die vorhandenen Felder der Bestandsdecke bleiben unangetastet.
  assert.equal(m.ceilings[0].dickeMm, 200);
  assert.equal(m.ceilings[0].id, 'c1');
});

test('Z-06-N1/K-06 ROT: ein bereits gesetzter Status wird von der Migration NICHT überschrieben', () => {
  // Ohne diese Zusage wäre `zu_pruefen` ein Bulldozer: ein bestätigtes Dach verlöre beim nächsten
  // Laden seine Freigabe — dieselbe Klasse von Verlust, gegen die B10 überhaupt geschrieben wurde.
  const v2 = {
    ...leereSzene(),
    schemaVersion: 2, // ← eine ALTE Datei, die den Status aber schon trägt
    roofs: [{ ...dach(), geometrieHerkunft: 'manuell', freigabe: 'bestaetigt' }],
  } as Record<string, unknown>;

  const m = migriereSzene(v2) as Record<string, any>;
  assert.equal(m.roofs[0].freigabe, 'bestaetigt');
  assert.equal(m.roofs[0].geometrieHerkunft, 'manuell');
});

test('v1 wird vom v3-Schema abgelehnt, nach Migration akzeptiert (Lade-Verweigerung ohne Migration)', () => {
  const v1 = { ...leereSzene(), schemaVersion: 1 } as Record<string, unknown>;
  delete v1.roofs;

  assert.equal(sceneDocumentSchema.safeParse(v1).success, false);
  assert.equal(sceneDocumentSchema.safeParse(migriereSzene(v1)).success, true);
});

test('v2-Dokument mit gültigem Dach parst; unbekannte Version bleibt abgelehnt', () => {
  const mitDach = { ...leereSzene(), roofs: [dach()] };
  assert.equal(sceneDocumentSchema.safeParse(mitDach).success, true);

  const v99 = migriereSzene({ ...leereSzene(), schemaVersion: 99 });
  assert.equal(sceneDocumentSchema.safeParse(v99).success, false);
});

// ---- Dach-Commands ----

test('ADD_ROOF fügt Dach hinzu; zweites Dach je Level wird abgelehnt (▲D1 max. 1)', () => {
  let [szene] = fuehreAus(leereSzene(), { type: 'ADD_ROOF', roof: dach('r1') });
  assert.equal(szene.roofs.length, 1);
  assert.equal(szene.roofs[0].roofType, 'sattel');

  assert.throws(
    () => fuehreAus(szene, { type: 'ADD_ROOF', roof: dach('r2') }),
    (e: unknown) => e instanceof CommandAbgelehnt && e.grund === 'dach_pro_level_vorhanden',
  );
});

test('ADD_ROOF auf unbekanntem Level abgelehnt; nicht-ganzzahlige Kontur abgelehnt', () => {
  assert.throws(
    () => fuehreAus(leereSzene(), { type: 'ADD_ROOF', roof: { ...dach(), levelId: 'og' } }),
    (e: unknown) => e instanceof CommandAbgelehnt && e.grund === 'level_unbekannt',
  );

  const krumm = dach();
  krumm.polygon = [{ x: 0, y: 0 }, { x: 8000.5, y: 0 }, { x: 8000, y: 10000 }, { x: 0, y: 10000 }];
  assert.throws(
    () => fuehreAus(leereSzene(), { type: 'ADD_ROOF', roof: krumm }),
    (e: unknown) => e instanceof CommandAbgelehnt && e.grund === 'nicht_ganzzahlig',
  );
});

test('UPDATE_ROOF ändert Neigung; REMOVE_ROOF entfernt; unbekanntes Dach abgelehnt', () => {
  let [szene] = fuehreAus(leereSzene(), { type: 'ADD_ROOF', roof: dach('r1') });

  [szene] = fuehreAus(szene, { type: 'UPDATE_ROOF', roofId: 'r1', changes: { neigungGrad: 40 } });
  assert.equal(szene.roofs[0].neigungGrad, 40);

  [szene] = fuehreAus(szene, { type: 'REMOVE_ROOF', roofId: 'r1' });
  assert.equal(szene.roofs.length, 0);

  assert.throws(
    () => fuehreAus(szene, { type: 'REMOVE_ROOF', roofId: 'r1' }),
    (e: unknown) => e instanceof CommandAbgelehnt && e.grund === 'dach_unbekannt',
  );
});

test('Undo/Redo: ADD_ROOF ist über inverse Patches umkehrbar (roofs Teil des Drafts)', () => {
  const start = leereSzene();
  const [nachher, patches, inverse] = fuehreAus(start, { type: 'ADD_ROOF', roof: dach('r1') });
  assert.equal(nachher.roofs.length, 1);

  const undo = applyPatches(nachher, inverse);
  assert.equal(undo.roofs.length, 0);

  const redo = applyPatches(undo, patches);
  assert.equal(redo.roofs.length, 1);
});

// ---- Wächter: bestehende Node-Welt unberührt (roofs additiv) ----

test('Node-Commands lassen roofs unangetastet; Wand-Welt unverändert', () => {
  let [szene] = fuehreAus(leereSzene(), { type: 'ADD_ROOF', roof: dach('r1') });
  [szene] = fuehreAus(szene, { type: 'ADD_NODE', node: wand('w1') });
  assert.equal(szene.roofs.length, 1);
  assert.equal(szene.nodes.length, 1);
});
