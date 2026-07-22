/**
 * Werkzeug-Registry (Plattform-Kern): Registrieren, Abfragen, Kategorie-Filter, Doppelschutz.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import {
  registriereWerkzeug, werkzeug, alleWerkzeuge, _leereRegistry, type WerkzeugNode,
} from '../geometry/werkzeugRegistry';

function bau(kind: string, kategorie: WerkzeugNode['kategorie'] = 'bau'): WerkzeugNode<{ ok: boolean }> {
  return {
    kind, schemaVersion: 1, kategorie, label: kind, beschreibung: `${kind}-Werkzeug`,
    parametrik: (d) => ({ bestanden: d.ok }),
    faehigkeiten: { waehlbar: true, ziehbar: true, dupliziert: false, loeschbar: true },
  };
}

test('registrieren + abfragen', () => {
  _leereRegistry();
  registriereWerkzeug(bau('treppe'));
  const w = werkzeug('treppe');
  assert.equal(w?.kind, 'treppe');
  assert.equal(w?.parametrik({ ok: true }).bestanden, true);
});

test('doppelte kind werfen (Programmierfehler)', () => {
  _leereRegistry();
  registriereWerkzeug(bau('wand'));
  assert.throws(() => registriereWerkzeug(bau('wand')), /bereits registriert/);
});

test('alleWerkzeuge hält Registrierreihenfolge und filtert nach Kategorie', () => {
  _leereRegistry();
  registriereWerkzeug(bau('wand', 'bau'));
  registriereWerkzeug(bau('fenster', 'bauelement'));
  registriereWerkzeug(bau('treppe', 'bau'));
  assert.deepEqual(alleWerkzeuge().map((w) => w.kind), ['wand', 'fenster', 'treppe']);
  assert.deepEqual(alleWerkzeuge('bau').map((w) => w.kind), ['wand', 'treppe']);
  assert.deepEqual(alleWerkzeuge('bauelement').map((w) => w.kind), ['fenster']);
});

test('unbekanntes kind → undefined', () => {
  _leereRegistry();
  assert.equal(werkzeug('gibtsnicht'), undefined);
});
