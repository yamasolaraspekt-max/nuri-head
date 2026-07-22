/**
 * Autarkes ConfiguratorPackage: Fabrik, Statusmodell, Freigabe-/Integrationsregeln.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import {
  neuesPaket, naechsteRevision, statusUebergangErlaubt, kannIntegrieren, markiereVeraltet,
  STATUS_UEBERGAENGE, CONFIGURATOR_SCHEMA_VERSION,
  type ConfiguratorStatus,
} from '../geometry/configuratorPackage';

const jetzt = '2026-07-22T10:00:00.000Z';

test('neuesPaket: frischer Entwurf mit deterministischen Feldern', () => {
  const p = neuesPaket({ id: 'p1', type: 'window', jetzt, autor: 'yama' });
  assert.equal(p.status, 'draft');
  assert.equal(p.revision, 1);
  assert.equal(p.schemaVersion, CONFIGURATOR_SCHEMA_VERSION);
  assert.equal(p.createdAt, jetzt);
  assert.equal(p.updatedBy, 'yama');
  assert.deepEqual(p.connectionPorts, []);
  assert.deepEqual(p.parameters, {});
});

test('autark: Paket braucht KEIN Projekt (projectId optional, kein sourceBuildingDocumentId)', () => {
  const p = neuesPaket({ id: 'p2', type: 'pv', jetzt, autor: 'yama' });
  assert.equal(p.projectId, undefined);
  assert.equal(p.sourceBuildingDocumentId, undefined);
});

test('Statusübergänge: erlaubte Wege gelten, verbotene nicht', () => {
  assert.ok(statusUebergangErlaubt('draft', 'checked'));
  assert.ok(statusUebergangErlaubt('checked', 'approved'));
  assert.ok(statusUebergangErlaubt('approved', 'integrated'));
  assert.ok(!statusUebergangErlaubt('draft', 'approved'), 'Entwurf darf nicht direkt freigegeben werden');
  assert.ok(!statusUebergangErlaubt('draft', 'integrated'));
  // Gleichbleiben immer erlaubt
  assert.ok(statusUebergangErlaubt('approved', 'approved'));
});

test('Freigabe-Schutz: aus approved/integrated geht es nur über outdated zurück', () => {
  assert.deepEqual([...STATUS_UEBERGAENGE.approved], ['integrated', 'outdated']);
  assert.deepEqual([...STATUS_UEBERGAENGE.integrated], ['outdated']);
  assert.ok(!statusUebergangErlaubt('approved', 'draft'));
  assert.ok(!statusUebergangErlaubt('integrated', 'checked'));
});

test('kannIntegrieren nur bei approved', () => {
  const zustaende: ConfiguratorStatus[] = ['draft', 'incomplete', 'generated', 'checked', 'integrated', 'outdated'];
  for (const s of zustaende) assert.ok(!kannIntegrieren({ status: s }), `${s} darf nicht integrierbar sein`);
  assert.ok(kannIntegrieren({ status: 'approved' }));
});

test('markiereVeraltet: freigegebenes Paket wird outdated, Entwurf bleibt unberührt', () => {
  const spaeter = '2026-07-22T12:00:00.000Z';
  const app = { ...neuesPaket({ id: 'p3', type: 'roof', jetzt, autor: 'yama' }), status: 'approved' as ConfiguratorStatus };
  const veraltet = markiereVeraltet(app, spaeter, 'system');
  assert.equal(veraltet.status, 'outdated');
  assert.equal(veraltet.updatedAt, spaeter);
  const entwurf = neuesPaket({ id: 'p4', type: 'roof', jetzt, autor: 'yama' });
  assert.equal(markiereVeraltet(entwurf, spaeter, 'system').status, 'draft');
});

test('naechsteRevision zählt hoch und stempelt', () => {
  const p = neuesPaket({ id: 'p5', type: 'kitchen', jetzt, autor: 'yama' });
  const r = naechsteRevision(p, '2026-07-22T11:00:00.000Z', 'yama');
  assert.equal(r.revision, 2);
  assert.equal(r.updatedAt, '2026-07-22T11:00:00.000Z');
});
