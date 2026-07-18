import { test } from 'node:test';
import assert from 'node:assert/strict';
import type { OpeningNode, SceneNode, WallNode } from '../domain/scene.types';
import { erkenneRaeume } from '../geometry/roomDetection';
import { projiziereRaum } from '../projection/raumProjektion';

const JETZT = '2026-07-16T12:00:00.000Z';

function wand(id: string, sx: number, sy: number, ex: number, ey: number): WallNode {
  return {
    id, type: 'wall', levelId: 'eg', visible: true, locked: false, tags: [],
    createdAt: JETZT, updatedAt: JETZT,
    start: { x: sx, y: sy }, end: { x: ex, y: ey }, thickness: 240, height: 2500,
  };
}

function fenster(id: string, wallId: string, offset: number, width = 1200): OpeningNode {
  return {
    id, type: 'window', levelId: 'eg', visible: true, locked: false, tags: [],
    createdAt: JETZT, updatedAt: JETZT,
    hostWallId: wallId, offsetFromWallStart: offset, width, height: 1400, sillHeight: 900,
  };
}

function rechteck(): WallNode[] {
  return [
    wand('sued', 0, 0, 4000, 0),
    wand('ost', 4000, 0, 4000, 5000),
    wand('nord', 4000, 5000, 0, 5000),
    wand('west', 0, 5000, 0, 0),
  ];
}

test('Fixture (Abnahmekriterium 9): Rechteck-Raum mit Südfenster — feldgleiches Soll-JSON, handgebaut', () => {
  const waende = rechteck();
  const nodes: SceneNode[] = [...waende, fenster('f1', 'sued', 1000)];
  const raeume = erkenneRaeume(waende, 2500);
  assert.equal(raeume.length, 1);

  const projektion = projiziereRaum(raeume[0], raeume, nodes, 0, 2500);

  // Vertrag: raum_geometrien-Felder (wberechnung/ticket) — Struktur exakt.
  assert.deepEqual(Object.keys(projektion).sort(), ['boden', 'decke', 'geschoss', 'hoehe_mm', 'polygon', 'wand_segmente'].sort());
  assert.equal(projektion.geschoss, 0);
  assert.equal(projektion.hoehe_mm, 2500);
  assert.equal(projektion.polygon.length, 4);
  assert.equal(projektion.decke, null);   // P0: ehrlich unbestimmt
  assert.equal(projektion.boden, null);

  assert.equal(projektion.wand_segmente.length, 4);
  assert.ok(projektion.wand_segmente.every((s) => s.grenzflaeche === 'aussen'), 'freistehendes Rechteck: alle Wände außen');

  // Azimut abgeleitet (Nord=+y): die 4 Außennormalen müssen exakt {0, 90, 180, 270} sein.
  const azimute = projektion.wand_segmente.map((s) => s.azimut_grad).sort((a, b) => (a ?? 0) - (b ?? 0));
  assert.deepEqual(azimute, [0, 90, 180, 270]);

  // Das Fenster liegt im Süd-Segment (Normale zeigt nach Süden = 180°).
  const sued = projektion.wand_segmente.find((s) => s.azimut_grad === 180);
  assert.ok(sued, 'Südsegment muss existieren');
  assert.deepEqual(sued!.oeffnungen, [{ typ: 'fenster', breite_mm: 1200, hoehe_mm: 1400, bruestung_mm: 900 }]);
  const andere = projektion.wand_segmente.filter((s) => s !== sued);
  assert.ok(andere.every((s) => s.oeffnungen.length === 0), 'kein anderes Segment trägt das Fenster');
});

test('Gegen-Beweis (Kriterium 9): Wand um 90° gedreht ⇒ Azimut folgt', () => {
  // Grundriss um 90° gedreht: Süd-Wand wird Ost-Wand → Fenster-Segment muss 90° tragen.
  const waende = [
    wand('a', 0, 0, 0, -4000),      // ehemals Süd (0,0)→(4000,0), um 90° cw gedreht: (0,0)→(0,-4000)
    wand('b', 0, -4000, 5000, -4000),
    wand('c', 5000, -4000, 5000, 0),
    wand('d', 5000, 0, 0, 0),
  ];
  const nodes: SceneNode[] = [...waende, fenster('f1', 'a', 1000)];
  const raeume = erkenneRaeume(waende, 2500);
  assert.equal(raeume.length, 1);

  const projektion = projiziereRaum(raeume[0], raeume, nodes, 0, 2500);
  const segmentMitFenster = projektion.wand_segmente.find((s) => s.oeffnungen.length === 1);
  assert.ok(segmentMitFenster);
  assert.equal(segmentMitFenster!.grenzflaeche, 'aussen');
  assert.equal(segmentMitFenster!.azimut_grad, 270, 'gedrehte Wand ⇒ gedrehter (abgeleiteter) Azimut');
});

test('Innenwand: geteilte Kante ist innen, Azimut null; Außenkanten bleiben außen', () => {
  const waende = [...rechteck(), wand('innen', 2000, 0, 2000, 5000)];
  const raeume = erkenneRaeume(waende, 2500);
  assert.equal(raeume.length, 2);

  const projektion = projiziereRaum(raeume[0], raeume, waende, 0, 2500);
  const innen = projektion.wand_segmente.filter((s) => s.grenzflaeche === 'innen');
  const aussen = projektion.wand_segmente.filter((s) => s.grenzflaeche === 'aussen');
  assert.equal(innen.length, 1, 'genau die geteilte Kante ist innen');
  assert.ok(innen.every((s) => s.azimut_grad === null), 'Innenwand trägt keinen Azimut');
  assert.ok(aussen.length >= 3);
  assert.ok(aussen.every((s) => s.azimut_grad !== null));
});
