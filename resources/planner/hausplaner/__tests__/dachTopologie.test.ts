/**
 * W-27/1 — die Ecken-Topologie des Dachgrundrisses.
 *
 * **Die tragende Zusage ist der UMLAUFSINN (K-4 aus W-27s Blatt):** dasselbe Polygon, einmal im und
 * einmal gegen den Uhrzeigersinn gezeichnet, muss **dieselben** Ecken liefern. Ohne Schritt 0
 * kippt die Innen/Außen-Unterscheidung — und zwar **leise**, es gibt kein Ergebnis, das ungültig
 * aussieht.
 *
 * Dazu: alle vier Ausgänge einschließlich `'neutral'`, `WALM`/`TEILWALM` als Traufe im weiteren
 * Sinn, und die fünf Zählungen der Analyse.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import {
  analyzeTopology,
  type EdgeTopologyConfig,
  type EdgeTopologyType,
  type TopologyPoint,
} from '../geometry/dachTopologie';

/** Ein Rechteck, gegen den Uhrzeigersinn. Vier Außenecken zu je 90°. */
const RECHTECK_CCW: TopologyPoint[] = [
  { x: 0, y: 0 }, { x: 10, y: 0 }, { x: 10, y: 6 }, { x: 0, y: 6 },
];

/**
 * Ein L-förmiger Grundriss, gegen den Uhrzeigersinn. Fünf Außenecken und **eine einspringende**
 * — genau der Fall, für den es die Innen/Außen-Unterscheidung gibt.
 */
const L_FORM_CCW: TopologyPoint[] = [
  { x: 0, y: 0 }, { x: 10, y: 0 }, { x: 10, y: 4 },
  { x: 4, y: 4 }, { x: 4, y: 8 }, { x: 0, y: 8 },
];

function kanten(typen: EdgeTopologyType[]): EdgeTopologyConfig[] {
  return typen.map((type, i) => ({ id: `k${i}`, type, pitch: 30, label: `Kante ${i}` }));
}

const alleTraufen = (n: number): EdgeTopologyConfig[] => kanten(Array(n).fill('TRAUFE'));

test('K-4 TRAGEND: der Umlaufsinn — dasselbe Polygon in beiden Richtungen ergibt dieselben Ecken', () => {
  const cw = [...L_FORM_CCW].reverse();
  const a = analyzeTopology(L_FORM_CCW, alleTraufen(6));
  const b = analyzeTopology(cw, alleTraufen(6));

  assert.equal(a.innenEcken, b.innenEcken, 'die Zahl der Innenecken haengt am Umlaufsinn');
  assert.equal(a.aussenEcken, b.aussenEcken, 'die Zahl der Aussenecken haengt am Umlaufsinn');
  assert.equal(a.kehlen, b.kehlen);
  assert.equal(a.grate, b.grate);
});

test('K-4: die L-Form hat GENAU EINE einspringende Ecke, in beiden Umlaufrichtungen', () => {
  const cw = [...L_FORM_CCW].reverse();
  assert.equal(analyzeTopology(L_FORM_CCW, alleTraufen(6)).innenEcken, 1);
  assert.equal(analyzeTopology(cw, alleTraufen(6)).innenEcken, 1);
});

test('das Rechteck hat vier Aussenecken zu je 90 Grad und keine Innenecke', () => {
  const a = analyzeTopology(RECHTECK_CCW, alleTraufen(4));
  assert.equal(a.aussenEcken, 4);
  assert.equal(a.innenEcken, 0);
  for (const c of a.corners) assert.ok(Math.abs(c.angleDeg - 90) < 1e-9, `${c.angleDeg} statt 90`);
});

test('vier Traufen ringsum: jede Aussenecke ist ein GRAT', () => {
  const a = analyzeTopology(RECHTECK_CCW, alleTraufen(4));
  assert.equal(a.grate, 4);
  assert.equal(a.kehlen, 0);
});

test('die einspringende Ecke der L-Form wird zur KEHLE, die uebrigen bleiben Grate', () => {
  const a = analyzeTopology(L_FORM_CCW, alleTraufen(6));
  assert.equal(a.kehlen, 1, 'die einspringende Ecke ist die Kehle');
  assert.equal(a.grate, 5);
  const kehle = a.corners.find((c) => c.joinType === 'kehle');
  assert.ok(kehle, 'es gibt eine Kehle');
  assert.equal(kehle.cornerType, 'innen', 'eine Kehle entsteht NUR an einer Innenecke');
});

test('WALM und TEILWALM zaehlen als Traufe im weiteren Sinn — sonst gaebe es keine Grate', () => {
  const nurWalm = analyzeTopology(RECHTECK_CCW, kanten(['WALM', 'WALM', 'WALM', 'WALM']));
  assert.equal(nurWalm.grate, 4, 'WALM allein muss Grate ergeben');

  const gemischt = analyzeTopology(RECHTECK_CCW, kanten(['TRAUFE', 'TEILWALM', 'TRAUFE', 'WALM']));
  assert.equal(gemischt.grate, 4, 'TRAUFE, WALM und TEILWALM sind untereinander gleichwertig');
});

test('Traufe trifft GIEBEL: die Ecke ist ein ORTGANG, in beiden Reihenfolgen', () => {
  const a = analyzeTopology(RECHTECK_CCW, kanten(['TRAUFE', 'GIEBEL', 'TRAUFE', 'GIEBEL']));
  assert.equal(a.ortgaenge, 4, 'jede Ecke stoesst hier auf einen Giebel');
  assert.equal(a.grate, 0);
  assert.equal(a.kehlen, 0);
});

test('der VIERTE Ausgang: was weder Grat noch Kehle noch Ortgang ist, ist neutral — nicht undefined', () => {
  const a = analyzeTopology(RECHTECK_CCW, kanten(['PULT_WAND', 'PULT_WAND', 'PULT_WAND', 'PULT_WAND']));
  assert.equal(a.grate + a.kehlen + a.ortgaenge, 0);
  for (const c of a.corners) {
    assert.equal(c.joinType, 'neutral', 'ohne Treffer bleibt der Default stehen');
  }
});

test('jede Ecke traegt IMMER einen der vier Ausgaenge — nie undefined', () => {
  const erlaubt = new Set(['grat', 'kehle', 'ortgang', 'neutral']);
  for (const typen of [
    ['TRAUFE', 'GIEBEL', 'PULT_WAND', 'WALM'],
    ['GIEBEL', 'GIEBEL', 'GIEBEL', 'GIEBEL'],
    ['TEILWALM', 'PULT_WAND', 'TRAUFE', 'GIEBEL'],
  ] as EdgeTopologyType[][]) {
    for (const c of analyzeTopology(RECHTECK_CCW, kanten(typen)).corners) {
      assert.ok(erlaubt.has(c.joinType), `${c.joinType} ist kein gueltiger Ausgang`);
    }
  }
});

test('die fuenf Zaehlungen decken die Ecken vollstaendig ab', () => {
  const a = analyzeTopology(L_FORM_CCW, alleTraufen(6));
  assert.equal(a.innenEcken + a.aussenEcken, a.corners.length, 'jede Ecke ist innen ODER aussen');
  assert.ok(a.grate + a.kehlen + a.ortgaenge <= a.corners.length, 'keine Ecke zaehlt doppelt');
  assert.equal(a.points, L_FORM_CCW, 'die Eingabe wird durchgereicht, nicht kopiert');
});

test('entartete Kante: zwei gleiche Punkte lassen die Rechnung nicht abstuerzen', () => {
  const entartet: TopologyPoint[] = [
    { x: 0, y: 0 }, { x: 0, y: 0 }, { x: 10, y: 0 }, { x: 10, y: 6 },
  ];
  const a = analyzeTopology(entartet, alleTraufen(4));
  assert.equal(a.corners.length, 4);
  for (const c of a.corners) assert.ok(Number.isFinite(c.angleDeg), 'kein NaN aus acos');
});
