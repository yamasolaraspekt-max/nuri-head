/**
 * W-3a — Dachaufbauten: Commands (Add/Remove/Update, undo-fähig über den Immer-Draft), Additiv-Beweis
 * (Dach ohne/mit aufbauten validiert gegen das Schema → kein 422) und die reine 3D-Geometrie einer
 * Schleppgaube (Körper + masshaltiges Loch aus der Engine).
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { applyCommand } from '../commands/applyCommand';
import { sceneDocumentSchema } from '../domain/validation';
import { dachMeshWelt, dachflaechen } from '../renderers/three-d/dachMesh';
import { flaecheZuFrame, aufbauKoerper } from '../renderers/three-d/dachAufbautenMesh';
import type { SceneDocument, RoofNode, RoofAufbau } from '../domain/scene.types';

const ISO = '2026-07-23T00:00:00.000Z';

function baseDoc(): SceneDocument {
  return {
    id: 'doc-1',
    projectId: 1,
    schemaVersion: 2,
    revision: 1,
    units: 'mm',
    settings: { gridSize: 100, snapEnabled: true, angleSnap: 15 },
    levels: [{ id: 'l1', name: 'DG', elevation: 0, defaultWallHeight: 2500, floorThickness: 200, sortOrder: 0 }],
    nodes: [],
    materials: [],
    roofs: [{
      id: 'r1', type: 'roof', levelId: 'l1', visible: true, locked: false, tags: [],
      createdAt: ISO, updatedAt: ISO,
      polygon: [{ x: 0, y: 0 }, { x: 10000, y: 0 }, { x: 10000, y: 8000 }, { x: 0, y: 8000 }],
      roofType: 'sattel', neigungGrad: 35, firstAzimutGrad: 90, ueberstandMm: 400, traufhoeheMm: 6000,
    }],
    metadata: { createdAt: ISO, updatedAt: ISO },
  };
}

const GAUBE: RoofAufbau = { id: 'a1', typ: 'schleppgaube', x: 0.5, y: 0.4, breiteMm: 2000, hoeheMm: 1200, tiefeMm: 1500 };

test('ADD_ROOF_AUFBAU / REMOVE_ROOF_AUFBAU setzen und entfernen den Aufbau', () => {
  const doc = baseDoc();
  applyCommand(doc, { type: 'ADD_ROOF_AUFBAU', roofId: 'r1', aufbau: GAUBE }, ISO);
  assert.equal(doc.roofs[0].aufbauten?.length, 1);
  assert.equal(doc.roofs[0].aufbauten?.[0].typ, 'schleppgaube');
  applyCommand(doc, { type: 'REMOVE_ROOF_AUFBAU', roofId: 'r1', aufbauId: 'a1' }, ISO);
  assert.equal(doc.roofs[0].aufbauten?.length, 0);
});

test('UPDATE_ROOF_AUFBAU ändert ein Feld', () => {
  const doc = baseDoc();
  applyCommand(doc, { type: 'ADD_ROOF_AUFBAU', roofId: 'r1', aufbau: GAUBE }, ISO);
  applyCommand(doc, { type: 'UPDATE_ROOF_AUFBAU', roofId: 'r1', aufbauId: 'a1', changes: { breiteMm: 2400 } }, ISO);
  assert.equal(doc.roofs[0].aufbauten?.[0].breiteMm, 2400);
});

test('REMOVE/UPDATE auf unbekannten Aufbau wird abgelehnt (aufbau_unbekannt)', () => {
  const doc = baseDoc();
  assert.throws(() => applyCommand(doc, { type: 'REMOVE_ROOF_AUFBAU', roofId: 'r1', aufbauId: 'x' }, ISO), /existiert nicht/);
  assert.throws(() => applyCommand(doc, { type: 'UPDATE_ROOF_AUFBAU', roofId: 'r1', aufbauId: 'x', changes: {} }, ISO), /existiert nicht/);
});

test('Aufbau-Maße nicht ganzzahlig ⇒ abgelehnt (mm-Invariante)', () => {
  const doc = baseDoc();
  assert.throws(
    () => applyCommand(doc, { type: 'ADD_ROOF_AUFBAU', roofId: 'r1', aufbau: { ...GAUBE, breiteMm: 2000.5 } }, ISO),
    /ganzen Millimetern/,
  );
});

test('Additiv-Beweis: Dach OHNE und MIT aufbauten validiert gegen das Schema (kein 422)', () => {
  const doc = baseDoc();
  assert.equal(sceneDocumentSchema.safeParse(doc).success, true, 'Bestands-Dach ohne aufbauten gültig');
  applyCommand(doc, { type: 'ADD_ROOF_AUFBAU', roofId: 'r1', aufbau: GAUBE }, ISO);
  assert.equal(sceneDocumentSchema.safeParse(doc).success, true, 'Dach mit aufbauten gültig');
});

test('Schleppgaube: Engine liefert Körper-Dreiecke + masshaltiges Loch-Polygon', () => {
  const roof = baseDoc().roofs[0] as RoofNode;
  const flaechen = dachflaechen(roof);
  assert.ok(flaechen.length >= 1, 'Sattel liefert rechteckige Dachflächen');
  const yRidge = dachMeshWelt(roof).firstHoeheMm / 1000;
  const af = flaecheZuFrame(flaechen[0], yRidge);
  const k = aufbauKoerper(af, GAUBE);
  assert.ok(k.tris.length > 0, 'Gaubenkörper (Dreiecke) nicht leer');
  assert.ok(k.holePolyUV.length >= 3, 'Loch-Fußabdruck ist ein Polygon (≥3 Ecken)');
  assert.equal(k.pruefpflichtig, false, 'Schleppgaube auf 35°-Sattel ist darstellbar');
});

test('Walmdach: keine rechteckigen Aufbauflächen (Prüf-Marker-Pfad)', () => {
  const roof = { ...baseDoc().roofs[0], roofType: 'walm' } as RoofNode;
  assert.deepEqual(dachflaechen(roof), []);
});

test('SSOT-Verriegelung: dachflaechen()-Ecken liegen exakt auf der dachMeshWelt-Fläche (flach/pult/sattel)', () => {
  // M1-Schutz: dachflaechen und dachMeshWelt speisen sich aus EINER Quelle (dachRoh). Divergieren sie je
  // wieder (Doppel-Herleitung), findet mind. eine Fläche-Ecke keinen Mesh-Vertex → dieser Test schlägt an.
  const gleich = (a: { x: number; y: number; z: number }, b: { x: number; y: number; z: number }) =>
    Math.abs(a.x - b.x) < 1e-6 && Math.abs(a.y - b.y) < 1e-6 && Math.abs(a.z - b.z) < 1e-6;
  for (const shape of ['flach', 'pult', 'sattel'] as const) {
    const roof = { ...baseDoc().roofs[0], roofType: shape } as RoofNode;
    const meshVerts = dachMeshWelt(roof).dreiecke.flat();
    const faces = dachflaechen(roof);
    assert.ok(faces.length >= 1, `${shape}: Fläche vorhanden`);
    for (const f of faces) {
      for (const ecke of [f.eaveLeft, f.eaveRight, f.ridgeRight, f.ridgeLeft]) {
        assert.ok(
          meshVerts.some((v) => gleich(v, ecke)),
          `${shape}: Ecke (${ecke.x},${ecke.y},${ecke.z}) muss auf der Mesh-Fläche liegen`,
        );
      }
    }
  }
});
