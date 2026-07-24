/**
 * L/T-Render-Platzierung (Folge des U-Fix, SSOT-Anker): l/t-Verschneidungsdächer müssen — wie die U —
 * am Gebäude-Umriss (Bbox-Mitte) sitzen, NICHT am Polygon-Schwerpunkt. Kein Innenhof bei L/T (U-spezifisch);
 * geprüft wird: Dach-Grundriss-Zentrum == Wand-Bbox-Mitte (mit Gegenprobe Schwerpunkt≠Mitte) + Deckung.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { dachMeshWelt } from '../renderers/three-d/dachMesh';
import type { RoofNode } from '../domain/scene.types';

const ISO = '2026-01-01T00:00:00.000Z';
// L-Umriss (Bbox 12×12 m): Hauptbalken 12×8, Anbau oben-rechts 4×4. Schwerpunkt ≠ Bbox-Mitte.
const L_UMRISS = [{ x: 0, y: 0 }, { x: 12000, y: 0 }, { x: 12000, y: 12000 }, { x: 8000, y: 12000 }, { x: 8000, y: 8000 }, { x: 0, y: 8000 }];
// T-Umriss (Bbox 12×12 m): Hauptbalken 12×8, Anbau mittig oben 4×4. Schwerpunkt ≠ Bbox-Mitte (in y).
const T_UMRISS = [{ x: 0, y: 0 }, { x: 12000, y: 0 }, { x: 12000, y: 8000 }, { x: 8000, y: 8000 }, { x: 8000, y: 12000 }, { x: 4000, y: 12000 }, { x: 4000, y: 8000 }, { x: 0, y: 8000 }];

function roof(roofType: 'l-shape' | 't-shape', polygon: Array<{ x: number; y: number }>): RoofNode {
  return {
    id: 'r1', type: 'roof', levelId: 'l1', visible: true, locked: false, tags: [], createdAt: ISO, updatedAt: ISO,
    polygon, roofType, neigungGrad: 35, firstAzimutGrad: 0, ueberstandMm: 500, traufhoeheMm: 6000,
    anbau: { length: 12000, width: 8000, lengthB: 4000, widthB: 4000 },
  };
}
function bbox(pts: ReadonlyArray<{ x: number; y: number }>) {
  let xMin = Infinity, xMax = -Infinity, yMin = Infinity, yMax = -Infinity;
  for (const p of pts) { xMin = Math.min(xMin, p.x); xMax = Math.max(xMax, p.x); yMin = Math.min(yMin, p.y); yMax = Math.max(yMax, p.y); }
  return { xMin, xMax, yMin, yMax, cx: (xMin + xMax) / 2, cy: (yMin + yMax) / 2 };
}
function schwerpunkt(poly: ReadonlyArray<{ x: number; y: number }>) {
  return poly.reduce((a, p) => ({ x: a.x + p.x / poly.length, y: a.y + p.y / poly.length }), { x: 0, y: 0 });
}

for (const [form, umriss] of [['l-shape', L_UMRISS], ['t-shape', T_UMRISS]] as const) {
  test(`${form}: rendert echte Flächen (Vorbedingung)`, () => {
    assert.ok(dachMeshWelt(roof(form, umriss)).dreiecke.length > 0);
  });

  test(`${form}: Dach-Grundriss-Zentrum == Wand-Bbox-Mitte (nicht Schwerpunkt)`, () => {
    const r = roof(form, umriss);
    const dB = bbox(dachMeshWelt(r).dreiecke.flat());
    const pB = bbox(r.polygon);
    assert.ok(Math.abs(dB.cx - pB.cx) < 100, `x ${dB.cx} vs ${pB.cx}`);
    assert.ok(Math.abs(dB.cy - pB.cy) < 100, `y ${dB.cy} vs ${pB.cy}`);
    // Gegenprobe: der Schwerpunkt weicht von der Bbox-Mitte ab → der Anker-Wechsel ist relevant (sonst blind).
    const sp = schwerpunkt(r.polygon);
    assert.ok(Math.abs(sp.x - pB.cx) > 100 || Math.abs(sp.y - pB.cy) > 100, 'Schwerpunkt ≠ Bbox-Mitte');
  });

  test(`${form}: Dach deckt den Wand-Footprint (Umriss + Überstand)`, () => {
    const r = roof(form, umriss);
    const dB = bbox(dachMeshWelt(r).dreiecke.flat());
    const pB = bbox(r.polygon);
    assert.ok(dB.xMin <= pB.xMin + 1 && dB.xMax >= pB.xMax - 1, `x-Deckung Dach[${dB.xMin},${dB.xMax}] ⊇ Wand[${pB.xMin},${pB.xMax}]`);
    assert.ok(dB.yMin <= pB.yMin + 1 && dB.yMax >= pB.yMax - 1, `y-Deckung Dach[${dB.yMin},${dB.yMax}] ⊇ Wand[${pB.yMin},${pB.yMax}]`);
  });
}
