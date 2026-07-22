/**
 * Mehrstufige Bemaßung: Öffnungskette (innen) + Gesamtmaß (außen), Wandstärke + Öffnungen.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { bemassung, type BemWand, type BemOeffnung } from '../geometry/bemassung';

// Rechteck 6000×4000 (Achsmaß), Außenwände 240 mm.
const rechteck: BemWand[] = [
  { id: 'b', start: { x: 0, y: 0 }, end: { x: 6000, y: 0 }, thickness: 240 },     // unten (waagerecht)
  { id: 't', start: { x: 0, y: 4000 }, end: { x: 6000, y: 4000 }, thickness: 240 }, // oben
  { id: 'l', start: { x: 0, y: 0 }, end: { x: 0, y: 4000 }, thickness: 240 },     // links (senkrecht)
  { id: 'r', start: { x: 6000, y: 0 }, end: { x: 6000, y: 4000 }, thickness: 240 },// rechts
];

test('Rechteck ohne Öffnung: innere Kette = Wandstärke|lichtes Maß|Wandstärke', () => {
  const b = bemassung(rechteck, []);
  assert.deepEqual(b.x.oeffnung.map((s) => s.laenge), [240, 5760, 240]);
  assert.deepEqual(b.y.oeffnung.map((s) => s.laenge), [240, 3760, 240]);
});

test('Gesamtmaß außen = Außenkante–Außenkante (6240 / 4240)', () => {
  const b = bemassung(rechteck, []);
  assert.equal(b.x.gesamt!.laenge, 6240);
  assert.equal(b.y.gesamt!.laenge, 4240);
});

test('Fenster auf der unteren Wand erscheint als eigenes Maß in der X-Öffnungskette', () => {
  const oeff: BemOeffnung[] = [{ hostWallId: 'b', offsetFromWallStart: 2000, width: 1010 }];
  const b = bemassung(rechteck, oeff);
  // Referenzen X: -120,120, 2000,3010, 5880,6120 → 240 |1880 |1010 |2870 |240
  assert.deepEqual(b.x.oeffnung.map((s) => s.laenge), [240, 1880, 1010, 2870, 240]);
  // eine Öffnungsbreite von exakt 1010 ist als Segment enthalten
  assert.ok(b.x.oeffnung.some((s) => s.laenge === 1010));
  // Gesamtmaß bleibt unverändert
  assert.equal(b.x.gesamt!.laenge, 6240);
});

test('Fenster auf senkrechter Wand geht in die Y-Kette, nicht in X', () => {
  const oeff: BemOeffnung[] = [{ hostWallId: 'l', offsetFromWallStart: 1000, width: 800 }];
  const b = bemassung(rechteck, oeff);
  assert.ok(b.y.oeffnung.some((s) => s.laenge === 800));
  assert.deepEqual(b.x.oeffnung.map((s) => s.laenge), [240, 5760, 240]); // X unberührt
});

test('keine Ecken-Artefakte: Rechteck-X-Kette hat genau 3 Segmente (kein Mittelstrich)', () => {
  const b = bemassung(rechteck, []);
  assert.equal(b.x.oeffnung.length, 3);
});

test('Innenwand fügt ihre Stärke als Maß hinzu (senkrechte Innenwand bei x=3000)', () => {
  const mitInnen: BemWand[] = [...rechteck, { id: 'i', start: { x: 3000, y: 0 }, end: { x: 3000, y: 4000 }, thickness: 115 }];
  const b = bemassung(mitInnen, []);
  // Innenwand-Flächen 2942,5/3057,5 → gerundet 2943/3058, Stärke-Segment 115 vorhanden
  assert.ok(b.x.oeffnung.some((s) => s.laenge === 115));
});

test('leere Eingabe → keine Ketten, bbox null', () => {
  const b = bemassung([], []);
  assert.deepEqual(b.x.oeffnung, []);
  assert.equal(b.x.gesamt, null);
  assert.equal(b.bbox, null);
});
