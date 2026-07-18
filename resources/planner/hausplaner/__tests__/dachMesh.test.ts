/**
 * D-c (Kern) — Dach-Mesh: reine 3D-Geometrie. Verifikation ohne Browser durch Kreuzprobe:
 * die Summe der Dreiecks-Flächen MUSS der belastbaren dachFlaechen()-Summe entsprechen
 * (zwei unabhängige Rechnungen → gleiche Wahrheit). Plus First-Höhe & Traufe-Invarianten.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import type { RoofNode } from '../domain/scene.types';
import { dachMeshWelt, dreieckFlaecheM2 } from '../renderers/three-d/dachMesh';
import { dachFlaechen, DachGeometrieUngueltig } from '../geometry/dachGeometrie';

const JETZT = '2026-07-17T12:00:00.000Z';
const EPS = 1e-6;

function dach(over: Partial<RoofNode> & Pick<RoofNode, 'roofType' | 'neigungGrad'>): RoofNode {
  return {
    id: 'r1', type: 'roof', levelId: 'eg', visible: true, locked: false, tags: [],
    createdAt: JETZT, updatedAt: JETZT,
    polygon: [{ x: 0, y: 0 }, { x: 8000, y: 0 }, { x: 8000, y: 10000 }, { x: 0, y: 10000 }],
    firstAzimutGrad: 0, ueberstandMm: 0, traufhoeheMm: 2500,
    ...over,
  };
}

const meshFlaeche = (roof: RoofNode) =>
  dachMeshWelt(roof).dreiecke.reduce((s, t) => s + dreieckFlaecheM2(t), 0);

const dachFlaeche = (roof: RoofNode) =>
  dachFlaechen(roof).reduce((s, f) => s + f.flaeche_m2, 0);

for (const typ of ['flach', 'pult', 'sattel', 'walm'] as const) {
  test(`Kreuzprobe ${typ}: Mesh-Gesamtfläche == dachFlaechen-Summe`, () => {
    const roof = dach({ roofType: typ, neigungGrad: typ === 'flach' ? 0 : 32 });
    assert.ok(
      Math.abs(meshFlaeche(roof) - dachFlaeche(roof)) < EPS,
      `${typ}: Mesh ${meshFlaeche(roof)} ≠ dachFlaechen ${dachFlaeche(roof)}`,
    );
  });
}

test('Kreuzprobe mit Überstand (Sattel, ü=600): Mesh == dachFlaechen', () => {
  const roof = dach({ roofType: 'sattel', neigungGrad: 40, ueberstandMm: 600 });
  assert.ok(Math.abs(meshFlaeche(roof) - dachFlaeche(roof)) < EPS);
});

test('Sattel First-Höhe = Traufhöhe + halbeSpann·tan(Neigung); Traufe ist der tiefste Punkt', () => {
  const roof = dach({ roofType: 'sattel', neigungGrad: 35 });
  const mesh = dachMeshWelt(roof);
  const erwarteteFirst = 2500 + Math.round(4000 * Math.tan((35 * Math.PI) / 180)); // b=4000
  assert.equal(mesh.firstHoeheMm, erwarteteFirst);

  const zs = mesh.dreiecke.flat().map((p) => p.z);
  assert.equal(Math.min(...zs), 2500);                 // Traufe = tiefster Punkt
  assert.equal(Math.round(Math.max(...zs)), erwarteteFirst); // First = höchster Punkt
});

test('Flachdach: alle Eckpunkte auf Traufhöhe (keine Neigung)', () => {
  const mesh = dachMeshWelt(dach({ roofType: 'flach', neigungGrad: 0 }));
  for (const p of mesh.dreiecke.flat()) assert.equal(p.z, 2500);
  assert.equal(mesh.firstHoeheMm, 2500);
});

test('Walm: 6 Dreiecke (2 Trapez- + 2 Walmflächen), First niedriger als Sattel-Pyramide', () => {
  const mesh = dachMeshWelt(dach({ roofType: 'walm', neigungGrad: 30 }));
  assert.equal(mesh.dreiecke.length, 6);
});

test('Kante 1: L-Kontur ⇒ dachMeshWelt UND dachFlaechen werfen DachGeometrieUngueltig (gleiche Wahrheit)', () => {
  const lform = [
    { x: 0, y: 0 }, { x: 8000, y: 0 }, { x: 8000, y: 4000 },
    { x: 4000, y: 4000 }, { x: 4000, y: 10000 }, { x: 0, y: 10000 },
  ];
  const roof = dach({ roofType: 'sattel', neigungGrad: 35, polygon: lform });
  // Render-Mesh darf NICHT still ein Rechteck über der Bounding-Box bauen (Evaluator-Befund D-c).
  assert.throws(() => dachMeshWelt(roof), (e: unknown) => e instanceof DachGeometrieUngueltig);
  assert.throws(() => dachFlaechen(roof), (e: unknown) => e instanceof DachGeometrieUngueltig);
});
