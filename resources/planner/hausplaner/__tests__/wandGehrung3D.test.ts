/**
 * 3D-Wandecken auf Gehrung (Ansatz A, Voll-Reuse): der 3D-Wandkörper bezieht seinen Grundriss aus
 * `wandBaender` (eine Wahrheit) — Enden gehrt, Innensegmente/Öffnungen rechtwinklig. Kern-Zusicherung:
 * zwei im Winkel stoßende Wände teilen an der Ecke den GEMEINSAMEN Gehrungspunkt (kein Loch/Overlap).
 * Reine Funktion, ohne Browser (analog wandBaender.test, auf die 3D-Grundrissprojektion).
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { wandBaender } from '../geometry/wallGeometry';
import { wandSegmentGrundriss, wandSegmentPrismaThree } from '../renderers/three-d/platzierung';
import type { WallNode } from '../domain/scene.types';

const ISO = '2026-01-01T00:00:00.000Z';
function wall(id: string, start: { x: number; y: number }, end: { x: number; y: number }): WallNode {
  return { id, type: 'wall', levelId: 'l1', visible: true, locked: false, tags: [], createdAt: ISO, updatedAt: ISO, start, end, thickness: 200, height: 2800 };
}
const dist = (a: { x: number; y: number }, b: { x: number; y: number }) => Math.hypot(a.x - b.x, a.y - b.y);
const eingabe = (w: WallNode) => ({ id: w.id, start: w.start, end: w.end, thickness: w.thickness });

test('Reuse: Voll-Segment-Grundriss == wandBaender-Ecken (kein zweiter Miter im 3D)', () => {
  const wA = wall('a', { x: 0, y: 0 }, { x: 4000, y: 0 });
  const wB = wall('b', { x: 4000, y: 0 }, { x: 4000, y: 4000 });
  const [bandA] = wandBaender([eingabe(wA), eingabe(wB)]);
  const g = wandSegmentGrundriss(bandA, wA, { vonMm: 0, bisMm: 4000 });
  assert.deepEqual(g, bandA.ecken, 'die 4 Ecken sind exakt die Band-Ecken');
});

test('Ecken-Dichtheit: zwei Wände an einer Ecke teilen den Gehrungspunkt (kein Loch/Overlap)', () => {
  const wA = wall('a', { x: 0, y: 0 }, { x: 4000, y: 0 });        // endet an (4000,0)
  const wB = wall('b', { x: 4000, y: 0 }, { x: 4000, y: 4000 });  // beginnt an (4000,0)
  const [bandA, bandB] = wandBaender([eingabe(wA), eingabe(wB)]);
  const gA = wandSegmentGrundriss(bandA, wA, { vonMm: 0, bisMm: 4000 });
  const gB = wandSegmentGrundriss(bandB, wB, { vonMm: 0, bisMm: 4000 });
  // A-Ende = gA[1],gA[2]; B-Anfang = gB[0],gB[3]. An der Gehrung müssen sich Punkte decken.
  const aEnde = [gA[1], gA[2]];
  const bAnfang = [gB[0], gB[3]];
  const geteilt = aEnde.filter((pa) => bAnfang.some((pb) => dist(pa, pb) < 1)).length;
  assert.ok(geteilt >= 1, `A-Ende und B-Anfang teilen ${geteilt} Gehrungspunkt(e) — sonst Loch/Overlap`);
});

test('Innensegment/Öffnungsgrenze bleibt rechtwinklig (Gehrung nur an Wand-Enden)', () => {
  const wA = wall('a', { x: 0, y: 0 }, { x: 4000, y: 0 });
  const [bandA] = wandBaender([eingabe(wA)]);
  // Inneres Segment (nicht am Wand-Ende) → Ecken sind die perpendikulare Kante, NICHT die Band-Ecke.
  const g = wandSegmentGrundriss(bandA, wA, { vonMm: 1000, bisMm: 3000 });
  assert.ok(Math.abs(g[0].x - 1000) < 1 && Math.abs(g[3].x - 1000) < 1, 'von-Kante rechtwinklig bei x=1000');
  assert.ok(Math.abs(g[1].x - 3000) < 1 && Math.abs(g[2].x - 3000) < 1, 'bis-Kante rechtwinklig bei x=3000');
  assert.ok(Math.abs(g[0].y - 100) < 1 && Math.abs(g[3].y + 100) < 1, 'quer = ±Wanddicke/2');
});

test('offenes Wandende ohne Nachbar → rechtwinklige Endkappe (Fallback, kein Crash)', () => {
  const wA = wall('a', { x: 0, y: 0 }, { x: 4000, y: 0 }); // allein — keine Nachbarn
  const [bandA] = wandBaender([eingabe(wA)]);
  const g = wandSegmentGrundriss(bandA, wA, { vonMm: 0, bisMm: 4000 });
  // Ohne Nachbar liefert wandBaender die quadratische (rechtwinklige) Ecke → Enden auf x=0 bzw. x=4000.
  assert.ok(Math.abs(g[0].x - 0) < 1 && Math.abs(g[1].x - 4000) < 1, 'stumpfe Enden bei 0 / 4000');
});

test('Prisma-Ecken: 8 three-Punkte (4 unten, 4 oben), Höhen aus unten/obenMm + Elevation', () => {
  const wA = wall('a', { x: 0, y: 0 }, { x: 4000, y: 0 });
  const [bandA] = wandBaender([eingabe(wA)]);
  const g = wandSegmentGrundriss(bandA, wA, { vonMm: 0, bisMm: 4000 });
  const e = wandSegmentPrismaThree(g, 0, 2800, 500);
  assert.equal(e.length, 8);
  // three ist y-up: die 4 unteren haben kleineres y als die 4 oberen (Höhe = z-Welt → y-three).
  const yUnten = e.slice(0, 4).map((p) => p.y), yOben = e.slice(4).map((p) => p.y);
  assert.ok(Math.max(...yUnten) < Math.min(...yOben), 'obere Ecken liegen höher als untere');
});
