/**
 * 3D-Capture-Flag — reine Ableitung (kein WebGL). Der nicht-leere Frame selbst ist Evaluator-
 * Browser-Sache; hier wird nur die Perf-relevante Weiche geprüft: preserveDrawingBuffer NUR mit Flag.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { istCaptureFlag, SNAPSHOT_GLOBAL } from '../renderers/three-d/capture';

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
