/**
 * Dach-Vorlagen (P2b-6). Reine Defaults je Form.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { DACH_VORLAGEN, dachVorlage, dachNeigungDefault, type DachForm } from '../geometry/dachVorlage';

test('alle vier Formen sind als Vorlage vorhanden, Sattel zuerst', () => {
  assert.deepEqual(DACH_VORLAGEN.map((v) => v.form), ['sattel', 'walm', 'pult', 'flach']);
});

test('Flachdach hat Neigung 0, die geneigten Formen > 0', () => {
  assert.equal(dachNeigungDefault('flach'), 0);
  for (const f of ['sattel', 'walm', 'pult'] as DachForm[]) {
    assert.ok(dachNeigungDefault(f) > 0, `${f} sollte geneigt sein`);
  }
});

test('jede Vorlage trägt Form, Label und ganzzahlige Neigung', () => {
  for (const v of DACH_VORLAGEN) {
    assert.equal(typeof v.label, 'string');
    assert.ok(v.label.length > 0);
    assert.ok(Number.isInteger(v.neigungGrad) && v.neigungGrad >= 0 && v.neigungGrad < 90);
  }
});

test('dachVorlage liefert die passende Vorlage', () => {
  assert.equal(dachVorlage('walm').label, 'Walmdach');
  assert.equal(dachVorlage('pult').neigungGrad, 15);
});

test('unbekannte Form fällt sicher auf Sattel zurück (nie undefined)', () => {
  // @ts-expect-error absichtlich ungültige Eingabe für den Fallback-Pfad
  const v = dachVorlage('gibtsnicht');
  assert.equal(v.form, 'sattel');
});
