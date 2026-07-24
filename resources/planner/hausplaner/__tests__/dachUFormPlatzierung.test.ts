/**
 * U-Render-Platzierung (L/T/U Teil 2, Evaluator-NACHBESSERN): das u-shape-Dach muss am Gebäude-Umriss
 * sitzen, NICHT am Polygon-Schwerpunkt. Diese Lücke war test-grün (dreiecke > 0), aber die Ausrichtung
 * falsch — genau das prüft dieser Test jetzt an der ECHTEN u-dach-Fixture (deterministische Sicht).
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { dachMeshWelt } from '../renderers/three-d/dachMesh';
import { ladeFixture } from '../fixtures/studioFixtures';
import type { RoofNode } from '../domain/scene.types';

const roof = ladeFixture('u-dach')!.roofs[0] as RoofNode;
const mesh = dachMeshWelt(roof);

function bbox(pts: ReadonlyArray<{ x: number; y: number }>) {
  let xMin = Infinity, xMax = -Infinity, yMin = Infinity, yMax = -Infinity;
  for (const p of pts) { xMin = Math.min(xMin, p.x); xMax = Math.max(xMax, p.x); yMin = Math.min(yMin, p.y); yMax = Math.max(yMax, p.y); }
  return { xMin, xMax, yMin, yMax, cx: (xMin + xMax) / 2, cy: (yMin + yMax) / 2 };
}
const dachGrund = mesh.dreiecke.flat().map((p) => ({ x: p.x, y: p.y }));
const dB = bbox(dachGrund);
const pB = bbox(roof.polygon);

test('U rendert überhaupt (Vorbedingung)', () => {
  assert.ok(mesh.dreiecke.length > 0);
});

test('Dach-Grundriss-Zentrum == Wand-Bbox-Zentrum (nicht Schwerpunkt)', () => {
  // Anker ist die Bbox-Mitte des Umrisses — mit der Schwerpunkt-Näherung läge das Zentrum verschoben.
  assert.ok(Math.abs(dB.cx - pB.cx) < 100, `x-Zentrum ${dB.cx} vs ${pB.cx}`);
  assert.ok(Math.abs(dB.cy - pB.cy) < 100, `y-Zentrum ${dB.cy} vs ${pB.cy}`);
  // Gegenprobe, dass der Fix wirkt: der Schwerpunkt weicht vom Bbox-Zentrum ab (sonst wäre der Test blind).
  const sp = roof.polygon.reduce((a, p) => ({ x: a.x + p.x / roof.polygon.length, y: a.y + p.y / roof.polygon.length }), { x: 0, y: 0 });
  assert.ok(Math.abs(sp.y - pB.cy) > 100, 'U-Schwerpunkt ≠ Bbox-Mitte — der Anker-Wechsel ist relevant');
});

test('Dach deckt den Wand-Footprint (Umriss + Überstand, nicht versetzt)', () => {
  // Das Dach ragt per Überstand nach außen ⇒ Dach-Bbox umschließt die Wand-Bbox.
  assert.ok(dB.xMin <= pB.xMin + 1 && dB.xMax >= pB.xMax - 1, `x-Deckung: Dach[${dB.xMin},${dB.xMax}] ⊇ Wand[${pB.xMin},${pB.xMax}]`);
  assert.ok(dB.yMin <= pB.yMin + 1 && dB.yMax >= pB.yMax - 1, `y-Deckung: Dach[${dB.yMin},${dB.yMax}] ⊇ Wand[${pB.yMin},${pB.yMax}]`);
});

test('Innenhof (Kerbe) bleibt frei — keine Dachfläche über der Hof-Mitte', () => {
  // U_UMRISS-Kerbe: x∈[3500,8500], y∈[6000,10000] → Hof-Mitte (6000, 8000). Bei falscher Orientierung
  // kippt eine Fläche in den Hof; die Grundriss-Zentren der Dreiecke lägen dann in der Kerbe.
  const hof = { x: 6000, y: 8000 };
  const drin = mesh.dreiecke.filter((t) => {
    const c = { x: (t[0].x + t[1].x + t[2].x) / 3, y: (t[0].y + t[1].y + t[2].y) / 3 };
    return Math.abs(c.x - hof.x) < 1500 && Math.abs(c.y - hof.y) < 1500;
  });
  assert.equal(drin.length, 0, `${drin.length} Dachdreiecke ragen in den Innenhof (Orientierung/Platzierung falsch)`);
});
