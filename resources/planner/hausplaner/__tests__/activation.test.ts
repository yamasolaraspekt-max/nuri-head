/**
 * UI-2 — Activation-Engine (§21). Je Bedingung ein GRÜNER Fall + roter Gegentest.
 * Grundsatz: eine deaktivierte Zusage zählt erst, wenn ein Gegentest zeigt, dass sie fallen KANN.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { resolveToolState } from '../app/tools/activation';
import { toolNach } from '../app/tools/toolRegistry';
import { baueAktivierungsKontext, type KontextEingabe } from '../app/tools/toolContext';

const basis: KontextEingabe = {
  workspace: 'architektur',
  view: '2d',
  selectionTypes: [],
  permissions: ['Hausplaner,update'],
  projectState: 'editable',
};
const ctx = (o: Partial<KontextEingabe> = {}) => baueAktivierungsKontext({ ...basis, ...o });

test('Auswahl ist überall aktiv (keine Workspace-/View-Bindung)', () => {
  const t = toolNach('auswahl')!;
  assert.equal(resolveToolState(t, ctx({ view: '3d', workspace: 'irgendwas' })).enabled, true);
});

test('Wand: aktiv in 2D-Architektur, INAKTIV in 3D (Ansichtsbindung) — mit Grund', () => {
  const t = toolNach('wand')!;
  assert.equal(resolveToolState(t, ctx({ view: '2d' })).enabled, true);
  const in3d = resolveToolState(t, ctx({ view: '3d' }));
  assert.equal(in3d.enabled, false);
  assert.match(in3d.reason ?? '', /Ansicht/);
});

test('Wand: INAKTIV in fremdem Arbeitsbereich (Gegentest zur Workspace-Regel)', () => {
  const t = toolNach('wand')!;
  const fremd = resolveToolState(t, ctx({ workspace: 'elektro' }));
  assert.equal(fremd.enabled, false);
  assert.match(fremd.reason ?? '', /Arbeitsbereich/);
});

test('Fenster: in 3D inaktiv über supportedViews (roter Gegentest)', () => {
  const t = toolNach('fenster')!;
  assert.equal(resolveToolState(t, ctx({ view: '2d' })).enabled, true);
  const in3d = resolveToolState(t, ctx({ view: '3d' }));
  assert.equal(in3d.enabled, false);
  assert.match(in3d.reason ?? '', /Ansicht/);
});

test('activationRules-Regeltypen view + workspace an synthetischem Werkzeug', () => {
  const synth = {
    id: 'synth', label: 'Synth', icon: 'x', groupId: 'test',
    supportedWorkspaces: [], supportedViews: [], helpText: 'test',
    activationRules: [
      { type: 'view', operator: 'equals', value: 'schnitt', grund: 'Nur im Schnitt.' },
      { type: 'workspace', operator: 'not-equals', value: 'pv', grund: 'Nicht im PV-Bereich.' },
    ],
  } as const;
  // erfüllt: view=schnitt, workspace≠pv
  assert.equal(resolveToolState(synth, ctx({ view: 'schnitt', workspace: 'architektur' })).enabled, true);
  // view-Regel verletzt
  const v = resolveToolState(synth, ctx({ view: '2d', workspace: 'architektur' }));
  assert.equal(v.enabled, false); assert.match(v.reason ?? '', /Schnitt/);
  // workspace-Regel verletzt
  const w = resolveToolState(synth, ctx({ view: 'schnitt', workspace: 'pv' }));
  assert.equal(w.enabled, false); assert.match(w.reason ?? '', /PV/);
});

test('Löschen: braucht Auswahl — leer ⇒ inaktiv, mit Auswahl ⇒ aktiv', () => {
  const t = toolNach('loeschen')!;
  const leer = resolveToolState(t, ctx({ selectionTypes: [] }));
  assert.equal(leer.enabled, false);
  assert.match(leer.reason ?? '', /Auswahl/);
  assert.equal(resolveToolState(t, ctx({ selectionTypes: ['wall'] })).enabled, true);
});

test('Löschen: ohne Recht inaktiv (Gegentest zur Permission-Regel)', () => {
  const t = toolNach('loeschen')!;
  const ohneRecht = resolveToolState(t, ctx({ selectionTypes: ['wall'], permissions: [] }));
  assert.equal(ohneRecht.enabled, false);
  assert.match(ohneRecht.reason ?? '', /Berechtigung/);
});

test('Löschen: gesperrtes Objekt inaktiv (object-state-Regel)', () => {
  const t = toolNach('loeschen')!;
  const gesperrt = resolveToolState(t, ctx({ selectionTypes: ['wall'], selectionStates: ['locked'] }));
  assert.equal(gesperrt.enabled, false);
  assert.match(gesperrt.reason ?? '', /gesperrt/);
});

test('Löschen: schreibgeschütztes Projekt inaktiv (project-state-Regel)', () => {
  const t = toolNach('loeschen')!;
  const ro = resolveToolState(t, ctx({ selectionTypes: ['wall'], projectState: 'readonly' }));
  assert.equal(ro.enabled, false);
  assert.match(ro.reason ?? '', /schreibgeschützt/);
});

test('Diskriminierung: bei erfüllten Bedingungen ist der Grund null', () => {
  const t = toolNach('loeschen')!;
  const ok = resolveToolState(t, ctx({ selectionTypes: ['wall', 'window'] }));
  assert.equal(ok.enabled, true);
  assert.equal(ok.reason, null);
});
