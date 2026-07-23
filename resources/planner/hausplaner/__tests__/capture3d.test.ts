/**
 * 3D-Capture-Flag — reine Ableitung (kein WebGL). Der nicht-leere Frame selbst ist Evaluator-
 * Browser-Sache; hier wird nur die Perf-relevante Weiche geprüft: preserveDrawingBuffer NUR mit Flag.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { istCaptureFlag, SNAPSHOT_GLOBAL, snapshotLeerMarker } from '../renderers/three-d/capture';

test('istCaptureFlag: nur capture=1 schaltet frei (Perf-Weiche)', () => {
  assert.equal(istCaptureFlag('?capture=1'), true);
  assert.equal(istCaptureFlag('capture=1'), true);
  assert.equal(istCaptureFlag('?foo=1&capture=1'), true);
  assert.equal(istCaptureFlag('?capture=0'), false, 'capture=0 = Normalbetrieb');
  assert.equal(istCaptureFlag('?cap=1'), false);
  assert.equal(istCaptureFlag(''), false, 'ohne Flag: kein preserveDrawingBuffer');
  assert.equal(istCaptureFlag(undefined), false);
  assert.equal(istCaptureFlag(null), false);
});

test('SNAPSHOT_GLOBAL ist stabil benannt (Evaluator-Vertrag)', () => {
  assert.equal(SNAPSHOT_GLOBAL, '__hausplanerSnapshot3d');
});

test('snapshotLeerMarker: 0-Container ⇒ Klartext-Marker (kein leerer PNG); echte Größe ⇒ null', () => {
  assert.equal(snapshotLeerMarker(907, 584), null, 'echte Größe ⇒ Snapshot fährt fort');
  const m0 = snapshotLeerMarker(0, 0);
  assert.ok(m0 && m0.startsWith('data:text/plain') && /nicht%20aktiv/.test(m0), '0×0 ⇒ Marker statt PNG');
  assert.ok(snapshotLeerMarker(2, 0), 'eine 0-Kante genügt');
  assert.ok(snapshotLeerMarker(0, 584), 'eine 0-Kante genügt');
});
