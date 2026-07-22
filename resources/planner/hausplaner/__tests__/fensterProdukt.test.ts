/**
 * Fenster-/Tür-Produktkern: Uw (DIN EN ISO 10077-1), RC-Machbarkeit (EN 1627), Positionspreis.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import {
  berechneUw, rcMachbar, preisFenster, profilNach, verglasungNach,
} from '../geometry/fensterProdukt';

test('Uw nach ISO 10077-1: 1230x1480, Kunststoff 70 + 2-fach → ~1,26', () => {
  const r = berechneUw({ breiteMm: 1230, hoeheMm: 1480, uf: 1.3, ug: 1.1, ansichtsbreiteMm: 117 });
  assert.equal(r.uw, 1.26);
  // Glasfläche + Rahmenfläche = Gesamtfläche 1,23·1,48 = 1,8204 m²
  assert.ok(Math.abs(r.agM2 + r.afM2 - 1.8204) < 1e-3);
});

test('3-fach senkt Uw deutlich (gleicher Rahmen)', () => {
  const zwei = berechneUw({ breiteMm: 1230, hoeheMm: 1480, uf: 1.3, ug: 1.1, ansichtsbreiteMm: 117 });
  const drei = berechneUw({ breiteMm: 1230, hoeheMm: 1480, uf: 1.3, ug: 0.6, ansichtsbreiteMm: 117 });
  assert.ok(drei.uw < zwei.uw);
  assert.equal(drei.uw, 0.92);
});

test('entartetes Mini-Fenster: kein Glas, Uw fällt auf Uf zurück, kein NaN', () => {
  const r = berechneUw({ breiteMm: 200, hoeheMm: 200, uf: 1.3, ug: 1.1, ansichtsbreiteMm: 117 });
  assert.equal(r.agM2, 0);
  assert.ok(Number.isFinite(r.uw));
});

test('RC-Machbarkeit (EN 1627): 2-fach trägt kein RC2, P4A schon', () => {
  assert.equal(rcMachbar('RC2', verglasungNach('2fach-standard')!), false);
  assert.equal(rcMachbar('RC2', verglasungNach('3fach-p4a')!), true);
  assert.equal(rcMachbar('RC3', verglasungNach('3fach-p5a')!), true);
  assert.equal(rcMachbar('ohne', verglasungNach('2fach-standard')!), true);
});

test('Positionspreis summiert Komponenten transparent', () => {
  const p = preisFenster({
    breiteMm: 1230, hoeheMm: 1480,
    profil: profilNach('kunststoff-70')!, verglasung: verglasungNach('2fach-standard')!,
    oeffnungsArt: 'dreh', rc: 'ohne',
  });
  assert.equal(p.beschlag, 45);
  assert.equal(p.rcAufpreis, 0);
  assert.equal(p.gesamt, p.rahmen + p.glas + p.beschlag + p.rcAufpreis);
  assert.ok(p.gesamt > 300 && p.gesamt < 360); // ~325 mit Beispielsätzen
});

test('RC-Aufpreis erhöht den Gesamtpreis', () => {
  const basis = preisFenster({ breiteMm: 1230, hoeheMm: 1480, profil: profilNach('kunststoff-82')!, verglasung: verglasungNach('3fach-p4a')!, oeffnungsArt: 'dreh-kipp', rc: 'ohne' });
  const rc2 = preisFenster({ breiteMm: 1230, hoeheMm: 1480, profil: profilNach('kunststoff-82')!, verglasung: verglasungNach('3fach-p4a')!, oeffnungsArt: 'dreh-kipp', rc: 'RC2' });
  assert.ok(rc2.gesamt > basis.gesamt);
  assert.equal(rc2.gesamt - basis.gesamt, 140);
});
