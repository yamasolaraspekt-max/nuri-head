/**
 * D-d — Dach-Projektions-Vertrag: eingefrorenes Fixture (dach_flaechen[]).
 * Grundlage: dach-andock-spec.md §2/§5. Friert den Vertrag ein — Änderungen fallen hier zuerst auf.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import type { SceneDocument, RoofNode } from '../domain/scene.types';
import { projiziereDach } from '../projection/dachProjektion';
import { DachGeometrieUngueltig } from '../geometry/dachGeometrie';

const JETZT = '2026-07-17T12:00:00.000Z';

function szene(roofs: RoofNode[], sortOrder = 0): SceneDocument {
  return {
    id: 'doc', projectId: 1, schemaVersion: 2, revision: 1, units: 'mm',
    settings: { gridSize: 100, snapEnabled: true, angleSnap: 15 },
    levels: [{ id: 'eg', name: 'EG', elevation: 0, defaultWallHeight: 2500, floorThickness: 200, sortOrder }],
    nodes: [], materials: [], roofs,
    metadata: { createdAt: JETZT, updatedAt: JETZT },
  };
}

function dach(over: Partial<RoofNode> = {}): RoofNode {
  return {
    id: 'r1', type: 'roof', levelId: 'eg', visible: true, locked: false, tags: [],
    createdAt: JETZT, updatedAt: JETZT,
    polygon: [{ x: 0, y: 0 }, { x: 8000, y: 0 }, { x: 8000, y: 10000 }, { x: 0, y: 10000 }],
    roofType: 'sattel', neigungGrad: 35, firstAzimutGrad: 0, ueberstandMm: 0, traufhoeheMm: 2500,
    ...over,
  };
}

test('Fixture: Satteldach 8×10 m/35° → eingefrorener dach_flaechen-Vertrag', () => {
  const dach_flaechen = projiziereDach(szene([dach()]));

  assert.deepEqual(dach_flaechen, [
    { geschoss: 0, roof_id: 'r1', dachtyp: 'sattel', flaeche_m2: 48.83, azimut_grad: 90, neigung_grad: 35, first_laenge_mm: 10000 },
    { geschoss: 0, roof_id: 'r1', dachtyp: 'sattel', flaeche_m2: 48.83, azimut_grad: 270, neigung_grad: 35, first_laenge_mm: 10000 },
  ]);
});

test('geschoss kommt aus level.sortOrder; leere Szene → leerer Vertrag', () => {
  assert.deepEqual(projiziereDach(szene([])), []);

  const p = projiziereDach(szene([dach()], 2));
  assert.equal(p.length, 2);
  for (const f of p) assert.equal(f.geschoss, 2);
});

test('Dach auf unbekanntem Geschoss wird NICHT projiziert (kein Rateswert)', () => {
  const p = projiziereDach(szene([dach({ levelId: 'dachgeschoss-fehlt' })]));
  assert.deepEqual(p, []);
});

test('Ungültige (nicht-rechteckige) Dachkontur propagiert DachGeometrieUngueltig (nie stilles Falschdach)', () => {
  const lform = [
    { x: 0, y: 0 }, { x: 8000, y: 0 }, { x: 8000, y: 4000 },
    { x: 4000, y: 4000 }, { x: 4000, y: 10000 }, { x: 0, y: 10000 },
  ];
  assert.throws(
    () => projiziereDach(szene([dach({ polygon: lform })])),
    (e: unknown) => e instanceof DachGeometrieUngueltig,
  );
});

test('Flachdach: Azimut null im Vertrag (horizontal), Fläche = Grundfläche', () => {
  const p = projiziereDach(szene([dach({ roofType: 'flach', neigungGrad: 0 })]));
  assert.equal(p.length, 1);
  assert.equal(p[0].azimut_grad, null);
  assert.equal(p[0].flaeche_m2, 80);
});
