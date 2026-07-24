/**
 * W-3b Teil 3 — L/T-Verschneidungsflächen (Port-Abschluss). Belegt: L/T rendern real (4 Flächen),
 * L vs. T unterscheiden sich über cx, Kanten (degeneriert/U → leer, kein NaN), und die Integration
 * über dachMeshWelt (Dreiecke belegt). Byte-Treue-Anker: main_N-Rechteck, ext_E-Poly, cx-Effekt.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { verschneidungsFlaechen, lTBauGueltig, type VerschneidungEingabe } from '../geometry/dachVerschneidung';
import { dachMeshWelt } from '../renderers/three-d/dachMesh';
import type { RoofNode } from '../domain/scene.types';

const ISO = '2026-01-01T00:00:00.000Z';
// Meter-Eingabe (Engine-Raum) — L=12, W=8, Anbau 4×4, 35°, oh/ohG 0.5.
function eingabe(form: 'l' | 't' | 'u'): VerschneidungEingabe {
  return { form, length: 12, width: 8, lengthB: 4, widthB: 4, overhang: 0.5, overhangGable: 0.5, pitchGrad: 35, height: 6, rafterHeight: 20 };
}
function roof(form: 'l-shape' | 't-shape' | 'u-shape'): RoofNode {
  return {
    id: 'r1', type: 'roof', levelId: 'l1', visible: true, locked: false, tags: [], createdAt: ISO, updatedAt: ISO,
    polygon: [{ x: 0, y: 0 }, { x: 12000, y: 0 }, { x: 12000, y: 8000 }, { x: 0, y: 8000 }],
    roofType: form, neigungGrad: 35, firstAzimutGrad: 0, ueberstandMm: 500, traufhoeheMm: 6000,
    anbau: { length: 12000, width: 8000, lengthB: 4000, widthB: 4000 },
  };
}

test('L/T rendern real: vier Flächen (main_N, main_S, ext_W, ext_E)', () => {
  for (const form of ['l', 't'] as const) {
    const f = verschneidungsFlaechen(eingabe(form));
    assert.deepEqual(f.map((x) => x.id), ['main_N', 'main_S', 'ext_W', 'ext_E'], form);
  }
});

test('main_N = Rechteck; uMax = L + 2·ohG = 13; uDir/normal wie Engine', () => {
  const n = verschneidungsFlaechen(eingabe('l'))[0];
  assert.equal(n.uMax, 13);
  assert.equal(n.origin.x, 6.5);        // L/2 + ohG
  assert.equal(n.origin.z, -4.5);       // -W/2 - oh
  assert.deepEqual(n.uDir, { x: -1, y: 0, z: 0 });
  assert.equal(n.poly.length, 4);       // reines Rechteck (kein Notch)
});

test('L vs. T unterscheiden sich über cx (main_S-Notch + ext_W-Ursprung)', () => {
  const l = verschneidungsFlaechen(eingabe('l'));
  const t = verschneidungsFlaechen(eingabe('t'));
  // L: rechte Seite bündig am Giebel (Grat) → 6-Punkt-Notch; T: beidseits innen → 7-Punkt-Notch.
  assert.equal(l[1].poly.length, 6, 'L main_S');
  assert.equal(t[1].poly.length, 7, 'T main_S');
  // ext_W-Ursprung: L cx=4 → x = 4 - 2 - 0.5 = 1.5; T cx=0 → x = -2.5.
  assert.equal(l[2].origin.x, 1.5);
  assert.equal(t[2].origin.x, -2.5);
});

test('ext_E-Poly byte-treu (identisch für L und T)', () => {
  // totalU = L_b + ohG + W_b/2 = 4 + 0.5 + 2 = 6.5; uValleyBot = W_b/2 + oh = 2.5.
  const e = verschneidungsFlaechen(eingabe('t'))[3];
  assert.equal(e.uMax, 6.5);
  assert.equal(e.poly[0].x, 0);
  assert.equal(e.poly[1].x, 6.5 - 2.5);   // totalU - uValleyBot = 4
  assert.equal(e.poly[2].x, 6.5);         // totalU
});

test('Kanten: degeneriert (W_b ≥ W) und form u → leer (kein NaN-Flächenbau)', () => {
  assert.deepEqual(verschneidungsFlaechen({ ...eingabe('l'), widthB: 8 }), []); // Anbau = Hauptdach
  assert.deepEqual(verschneidungsFlaechen({ ...eingabe('l'), pitchGrad: 0 }), []); // zu flach
  assert.deepEqual(verschneidungsFlaechen(eingabe('u')), []); // U läuft über uFormFlaechen, nicht hier
  assert.equal(lTBauGueltig(eingabe('l')), true);
  assert.equal(lTBauGueltig({ ...eingabe('l'), widthB: 8 }), false);
});

test('Integration dachMeshWelt: l/t liefern belegte Dreiecke (das [] ist ersetzt); u unberührt', () => {
  assert.ok(dachMeshWelt(roof('l-shape')).dreiecke.length > 0, 'l-shape rendert');
  assert.ok(dachMeshWelt(roof('t-shape')).dreiecke.length > 0, 't-shape rendert');
  assert.ok(dachMeshWelt(roof('u-shape')).dreiecke.length > 0, 'u-shape weiterhin');
});
