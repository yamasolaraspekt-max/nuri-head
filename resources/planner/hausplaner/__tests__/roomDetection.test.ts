import { test } from 'node:test';
import assert from 'node:assert/strict';
import type { WallNode } from '../domain/scene.types';
import { erkenneRaeume } from '../geometry/roomDetection';

const JETZT = '2026-07-16T12:00:00.000Z';

function wand(id: string, sx: number, sy: number, ex: number, ey: number): WallNode {
  return {
    id, type: 'wall', levelId: 'eg', visible: true, locked: false, tags: [],
    createdAt: JETZT, updatedAt: JETZT,
    start: { x: sx, y: sy }, end: { x: ex, y: ey }, thickness: 240, height: 2500,
  };
}

/** Referenzgrundriss der Spec: Rechteck 4000 × 5000 (Achsmaß). */
function rechteck(): WallNode[] {
  return [
    wand('sued', 0, 0, 4000, 0),
    wand('ost', 4000, 0, 4000, 5000),
    wand('nord', 4000, 5000, 0, 5000),
    wand('west', 0, 5000, 0, 0),
  ];
}

test('Referenz: Rechteck 4000×5000 ⇒ 1 Raum, Fläche 20.000.000 mm² (handgerechnet), Volumen 50e9 mm³', () => {
  const raeume = erkenneRaeume(rechteck(), 2500);
  assert.equal(raeume.length, 1);
  assert.equal(raeume[0].flaecheMm2, 20_000_000);           // 4000 · 5000, von Hand
  assert.equal(raeume[0].volumenMm3, 50_000_000_000);       // · 2500
  assert.equal(raeume[0].polygon.length, 4);
});

test('Referenz + Innenwand als T: 2 Räume à 10.000.000 mm² (Abnahmekriterium 5, handgerechnet)', () => {
  const waende = [...rechteck(), wand('innen', 2000, 0, 2000, 5000)]; // T-Punkte auf Süd- und Nordwand
  const raeume = erkenneRaeume(waende, 2500);
  assert.equal(raeume.length, 2, 'T-Kreuzung muss die Süd-/Nordwand teilen und zwei Räume ergeben');
  const flaechen = raeume.map((r) => r.flaecheMm2).sort();
  assert.deepEqual(flaechen, [10_000_000, 10_000_000]);     // je 2000 · 5000, von Hand
  const summe = raeume.reduce((s, r) => s + r.flaecheMm2, 0);
  assert.equal(summe, 20_000_000, 'Teilflächen müssen die Gesamtfläche exakt ergeben');
});

test('Kante: offener Wandzug ⇒ 0 Räume und Terminierung (keine Endlosschleife)', () => {
  const offen = [wand('a', 0, 0, 4000, 0), wand('b', 4000, 0, 4000, 5000), wand('c', 4000, 5000, 0, 5000)];
  const raeume = erkenneRaeume(offen, 2500);
  assert.equal(raeume.length, 0);
});

test('Kante: Stichwand in einen Raum hinein ändert die Raumzahl nicht', () => {
  const waende = [...rechteck(), wand('stich', 0, 2500, 1500, 2500)]; // hängt nur am Westrand (T), endet frei
  const raeume = erkenneRaeume(waende, 2500);
  assert.equal(raeume.length, 1, 'Stichwand darf keinen Phantom-Raum erzeugen');
  assert.equal(raeume[0].flaecheMm2, 20_000_000);
});

test('Kante: einzelne freie Wand ⇒ 0 Räume', () => {
  assert.equal(erkenneRaeume([wand('solo', 0, 0, 3000, 0)], 2500).length, 0);
});
